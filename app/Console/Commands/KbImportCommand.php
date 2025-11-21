<?php

namespace App\Console\Commands;

use App\Models\KbChunk;
use App\Models\KbDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class KbImportCommand extends Command
{
    protected $signature = 'ai:kb:import 
                            {--dir= : Đường dẫn thư mục chứa .txt/.md}
                            {--from= : Nguồn dữ liệu: products|services|faqs}
                            {--truncate : Xoá toàn bộ KB trước khi import}';

    protected $description = 'Import dữ liệu kiến thức cho chatbot từ thư mục hoặc từ CSDL (products/services/faqs).';

    public function handle(): int
    {
        if ($this->option('truncate')) {
            KbChunk::truncate();
            KbDocument::truncate();
            $this->warn('Đã xoá toàn bộ kb_documents/kb_chunks.');
        }

        $from = $this->option('from');
        $dir  = $this->option('dir');

        if ($from) {
            return $this->importFromDb($from);
        }

        // Fallback: import từ thư mục
        $dir = $dir ?: storage_path('app/ai_kb');
        if (!File::isDirectory($dir)) {
            $this->error('Dir not found: '.$dir);
            $this->line('→ Bạn có thể chạy: php artisan ai:kb:import --from=products');
            return self::FAILURE;
        }
        return $this->importFromDirectory($dir);
    }

    /* ====================== Import từ DB ====================== */

    protected function importFromDb(string $from): int
    {
        return match (Str::lower($from)) {
            'products' => $this->importProducts(),
            'services' => $this->importServices(),
            'faqs'     => $this->importFaqs(),
            default    => $this->failInvalidFrom($from),
        };
    }

    protected function importProducts(): int
    {
        // Tuỳ vào tên model của bạn
        $q = \App\Models\Product::query()
            ->with(['brand','category','imagesRel','mainImageRel'])
            ->where('status', 1);

        $count = 0;
        $this->info('Importing products → KB ...');

        $q->chunkById(200, function ($rows) use (&$count) {
            foreach ($rows as $p) {
                $doc = KbDocument::updateOrCreate(
                    ['source' => 'product:'.$p->product_id],
                    [
                        'title' => $p->product_name,
                        'meta'  => [
                            'product_id' => $p->product_id,
                            'brand'      => $p->brand->brand_name ?? null,
                            'category'   => $p->category->category_name ?? null,
                            'price'      => $p->price,
                            'capacity'   => $p->capacity,
                        ],
                    ]
                );

                // Xoá chunks cũ để re-import idempotent
                $doc->chunks()->delete();

                $pieces = array_filter([
                    "Sản phẩm: {$p->product_name}.",
                    $p->brand?->brand_name ? "Thương hiệu: {$p->brand->brand_name}." : null,
                    $p->category?->category_name ? "Danh mục: {$p->category->category_name}." : null,
                    is_numeric($p->price) ? "Giá niêm yết: ".number_format($p->price,0,',','.')."₫." : null,
                    $p->discount_percent ? "Giảm giá: {$p->discount_percent}%." : null,
                    $p->capacity ? "Dung tích/Quy cách: {$p->capacity}." : null,
                    $p->description ? "Mô tả: ".strip_tags($p->description) : null,
                    $p->specifications ? "Thông số: ".(is_array($p->specifications) ? json_encode($p->specifications, JSON_UNESCAPED_UNICODE) : (string)$p->specifications) : null,
                ]);

                $text = implode("\n", $pieces);

                $this->chunkAndSave($doc->id, $text);
                $count++;
            }
        });

        $this->info("✔ Imported $count products.");
        return self::SUCCESS;
    }

    protected function importServices(): int
    {
        // Tuỳ vào tên model của bạn
        $q = \App\Models\Service::query()->where('status', 1);

        $count = 0;
        $this->info('Importing services → KB ...');

        $q->chunkById(200, function ($rows) use (&$count) {
            foreach ($rows as $s) {
                $doc = KbDocument::updateOrCreate(
                    ['source' => 'service:'.$s->service_id],
                    [
                        'title' => $s->service_name ?? $s->name ?? ('Dịch vụ #'.$s->service_id),
                        'meta'  => [
                            'service_id' => $s->service_id,
                            'price'      => $s->price ?? null,
                            'duration'   => $s->duration ?? null,
                        ],
                    ]
                );

                $doc->chunks()->delete();

                $pieces = array_filter([
                    "Dịch vụ: ".($s->service_name ?? $s->name).".",
                    isset($s->price) ? "Giá tham khảo: ".number_format($s->price,0,',','.')."₫." : null,
                    isset($s->duration) ? "Thời lượng dự kiến: {$s->duration} phút." : null,
                    $s->short_desc ?? null,
                    $s->description ? "Mô tả chi tiết: ".strip_tags($s->description) : null,
                    $s->benefits ? "Lợi ích: ".strip_tags($s->benefits) : null,
                    $s->notes ? "Lưu ý: ".strip_tags($s->notes) : null,
                ]);

                $text = implode("\n", $pieces);

                $this->chunkAndSave($doc->id, $text);
                $count++;
            }
        });

        $this->info("✔ Imported $count services.");
        return self::SUCCESS;
    }

    protected function importFaqs(): int
    {
        $this->info('Seeding FAQs → KB ...');

        $faqs = [
            [
                'title' => 'Thời gian làm việc',
                'content' => "Lyn & Spa mở cửa 9:00–20:00 (T2–CN). Cuối tuần thường đông, khuyến khích đặt lịch trước.",
                'source'  => 'faq:hours'
            ],
            [
                'title' => 'Chính sách đổi trả',
                'content' => "Sản phẩm chưa mở nắp được đổi trong 7 ngày, còn hóa đơn. Mỹ phẩm đã mở nắp không hỗ trợ đổi trả vì lý do an toàn.",
                'source'  => 'faq:return'
            ],
            [
                'title' => 'Đặt lịch',
                'content' => "Bạn có thể đặt lịch trên website, chọn khung giờ mong muốn. Nhân viên sẽ gọi xác nhận.",
                'source'  => 'faq:booking'
            ],
        ];

        foreach ($faqs as $f) {
            $doc = KbDocument::updateOrCreate(
                ['source' => $f['source']],
                ['title'  => $f['title'], 'meta' => []]
            );
            $doc->chunks()->delete();
            $this->chunkAndSave($doc->id, $f['content']);
        }

        $this->info('✔ Seeded FAQs.');
        return self::SUCCESS;
    }

    protected function failInvalidFrom(string $from): int
    {
        $this->error("Giá trị --from không hợp lệ: $from");
        $this->line('Hỗ trợ: products | services | faqs');
        return self::FAILURE;
    }

    /* ====================== Import từ thư mục ====================== */

    protected function importFromDirectory(string $dir): int
    {
        $files = collect(File::allFiles($dir))
            ->filter(fn ($f) => in_array(strtolower($f->getExtension()), ['txt','md']));

        if ($files->isEmpty()) {
            $this->warn('Thư mục trống. Bạn có thể dùng --from=products để import từ DB.');
            return self::SUCCESS;
        }

        $this->info("Importing from folder: $dir");

        foreach ($files as $f) {
            $title = Str::of($f->getFilenameWithoutExtension())->replace('_',' ')->replace('-',' ')->title();
            $content = trim(File::get($f->getPathname()));

            $doc = KbDocument::updateOrCreate(
                ['source' => 'file:'.$f->getRelativePathname()],
                ['title' => (string)$title, 'meta' => ['path' => $f->getPathname()]]
            );

            $doc->chunks()->delete();
            $this->chunkAndSave($doc->id, $content);
            $this->line('  + '.$f->getFilename());
        }

        $this->info('✔ Done.');
        return self::SUCCESS;
    }

    /* ====================== Helpers ====================== */

    /**
     * Tách text thành các đoạn ~800–1200 ký tự và lưu.
     */
    protected function chunkAndSave(int $docId, string $text): void
    {
        $chunks = $this->splitIntoChunks($text, 900, 200); // target=900, overlap=200
        $ord = 0;
        foreach ($chunks as $c) {
            KbChunk::create([
                'kb_document_id' => $docId,
                'ord'            => $ord++,
                'content'        => $c,
                // ước lượng tokens (đủ dùng cho sắp xếp/giới hạn)
                'tokens'         => (int) ceil(strlen($c) / 4),
            ]);
        }
    }

    /**
     * Tách theo câu/đoạn, tránh đứt câu giữa chừng, có overlap nhẹ.
     */
    protected function splitIntoChunks(string $text, int $target = 900, int $overlap = 150): array
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        if ($text === '') return [];

        $sentences = preg_split('/(?<=[\.!\?…])\s+/u', $text) ?: [$text];

        $chunks = [];
        $buf = '';

        foreach ($sentences as $s) {
            if (mb_strlen($buf.' '.$s) <= $target || $buf === '') {
                $buf = trim($buf === '' ? $s : ($buf.' '.$s));
                continue;
            }
            $chunks[] = $buf;

            // tạo overlap nhẹ cho ngữ cảnh
            $bufTail = mb_substr($buf, max(0, mb_strlen($buf) - $overlap));
            $buf = trim($bufTail.' '.$s);
        }

        if ($buf !== '') $chunks[] = $buf;

        return $chunks;
    }
}
