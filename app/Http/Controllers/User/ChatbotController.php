<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use App\Models\ChatbotTraining;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function index(Request $request)
    {
        $chatSessionId = $request->session()->get('chat_session_id');

        if ($chatSessionId) {
            $session = ChatSession::find($chatSessionId);
        }

        if (empty($session)) {
            $session = ChatSession::create([
                'user_id' => optional($request->user())->id,
                'title'   => null
            ]);

            $request->session()->put('chat_session_id', $session->id);
        }

        $messages = $session->messages()->latest('id')->take(30)->get()->reverse();

        return view('Users.chat.index', compact('session', 'messages'));
    }



public function send(Request $req)
{
    $msg = trim($req->message);
    $msgLower = Str::lower($msg);

    /** ================== 0. CONTEXT: HỎI MỨC ĐỘ MỤN ================== */
    if (session('chat_context') === 'ask_acne_level') {

        $level = Str::lower(trim($msg));
        session()->forget('chat_context');

        if (in_array($level, ['nhẹ', 'mụn nhẹ'])) {

            $products = \App\Models\Product::where('status', 1)
                ->where(function ($q) {
                    $q->where('product_name', 'LIKE', '%mụn%')
                      ->orWhere('product_name', 'LIKE', '%acne%');
                })
                ->take(5)
                ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'assistant' => "Hiện chưa có sản phẩm phù hợp cho da mụn nhẹ 🌿"
                ]);
            }

            return response()->json([
                'assistant' =>
                    "🌸 *Da mụn nhẹ – bạn nên dùng các sản phẩm sau:*\n\n" .
                    $products->map(function ($p) {
                        $price = $p->discount_price ?: $p->price;
                        return "▫️ *{$p->product_name}* – " . number_format($price, 0, ',', '.') . "₫";
                    })->implode("\n")
            ]);
        }

        if (in_array($level, ['viêm', 'nặng', 'mụn viêm'])) {
            return response()->json([
                'assistant' =>
                    "⚠️ Với mụn viêm, bạn nên ưu tiên thăm khám hoặc dùng sản phẩm đặc trị theo tư vấn chuyên gia nha 💙"
            ]);
        }

        return response()->json([
            'assistant' => "Mình chưa rõ mức độ mụn 😥  
Bạn chọn giúp mình: **nhẹ / viêm / ẩn** nhé 😊"
        ]);
    }

    /** ================== 1. CHATBOT TRAINING (FAQ) ================== */
    $trainings = ChatbotTraining::all();

    foreach ($trainings as $t) {
        // Ưu tiên contains
        if (Str::contains($msgLower, Str::lower($t->question))) {
            return response()->json(['assistant' => $t->answer]);
        }

        // Fuzzy match
        similar_text($msgLower, Str::lower($t->question), $percent);
        if ($percent >= 80) {
            return response()->json(['assistant' => $t->answer]);
        }
    }

    /** ================== 2. ĐẶT LỊCH ================== */
    if (preg_match('/(.+)\s+(\d{1,2}\/\d{1,2})\s+lúc\s+(\d{1,2})h\s+(.*)/ui', $msg, $m)) {
        return response()->json([
            'assistant' =>
                "✨ *ĐÃ NHẬN THÔNG TIN ĐẶT LỊCH!*\n\n" .
                "👤 Tên: *{$m[1]}*\n" .
                "📅 Ngày: *{$m[2]}*\n" .
                "⏰ Giờ: *{$m[3]}:00*\n" .
                "💆 Dịch vụ: *{$m[4]}*\n\n" .
                "Bạn xác nhận giúp mình nhé ❤️"
        ]);
    }

    /** ================== 3. TRA ĐƠN ================== */
    if (preg_match('/#(\d+)/', $msg, $m)) {
        session(['pending_order_check' => $m[1]]);
        return response()->json([
            'assistant' =>
                "🔍 Bạn muốn tra đơn *#{$m[1]}* đúng không?\n" .
                "Trả lời **đúng rồi** hoặc **sai rồi** nhé!"
        ]);
    }

    if (session('pending_order_check')) {
        if (in_array($msgLower, ['đúng', 'đúng rồi', 'ok', 'oke'])) {
            $id = session()->pull('pending_order_check');
            return response()->json([
                'assistant' => "📦 Đơn hàng *#$id* đang được xử lý và chuẩn bị giao."
            ]);
        }
    }

    /** ================== 4. DA MỤN → HỎI MỨC ĐỘ ================== */
    if (preg_match('/da\s*(mụn|dầu|nhạy cảm)|mụn/ui', $msg)) {

        session(['chat_context' => 'ask_acne_level']);

        return response()->json([
            'assistant' =>
                "Da mụn nên ưu tiên sản phẩm dịu nhẹ, không gây bít tắc lỗ chân lông 🌿  
Bạn cho mình biết **mức độ mụn** (**nhẹ / viêm / ẩn**) nhé?"
        ]);
    }

    /** ================== 5. GỢI Ý SẢN PHẨM CHUNG ================== */
    if (Str::contains($msgLower, ['sản phẩm', 'serum', 'kem', 'dưỡng'])) {

        $products = \App\Models\Product::where('status', 1)
            ->orderByDesc('is_featured')
            ->take(5)
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'assistant' => "Hiện chưa có sản phẩm phù hợp 😥"
            ]);
        }

        $reply = "🎁 *Gợi ý sản phẩm cho bạn:*\n\n";
        foreach ($products as $p) {
            $price = $p->discount_price ?: $p->price;
            $reply .= "▫️ *{$p->product_name}*\n";
            $reply .= "💰 " . number_format($price, 0, ',', '.') . "₫\n\n";
        }

        return response()->json(['assistant' => $reply]);
    }

    /** ================== 6. AI FALLBACK ================== */
    $ai = app(GeminiService::class)->generateText($msg);
    return response()->json(['assistant' => $ai]);
}


}
