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

        $training = ChatbotTraining::all();
        foreach ($training as $t) {

            // So khớp gần đúng theo từ khóa
            if (Str::contains(Str::lower($msg), Str::lower($t->question))) {
                return response()->json(['assistant' => $t->answer]);
            }

            // Fuzzy match 60%
            similar_text(Str::lower($msg), Str::lower($t->question), $percent);
            if ($percent >= 60) {
                return response()->json(['assistant' => $t->answer]);
            }
        }

        if (preg_match('/(.+)\s+(\d{1,2}\/\d{1,2})\s+lúc\s+(\d{1,2})h\s+(.*)/ui', $msg, $m)) {

            $name = trim($m[1]);
            $date = trim($m[2]);
            $hour = trim($m[3]).":00";
            $service = trim($m[4]);

            return response()->json([
                "assistant" =>
                    "✨ ĐÃ NHẬN THÔNG TIN ĐẶT LỊCH!\n\n".
                    "👤 Tên: *$name*\n".
                    "📅 Ngày: *$date*\n".
                    "⏰ Giờ: *$hour*\n".
                    "💆 Dịch vụ: *$service*\n\n".
                    "Bạn xác nhận giúp mình để tạo lịch nhé ❤️"
            ]);
        }

        if (preg_match('/#(\d+)/', $msg, $m)) {

            $orderId = $m[1];
            session(['pending_order_check' => $orderId]);

            return response()->json([
                'assistant' => "🔍 Bạn muốn tra đơn hàng *#$orderId* đúng không?  
                Trả lời: **đúng rồi** hoặc **sai rồi** nhé!"
            ]);
        }


        // Khi user xác nhận
        if (session('pending_order_check')) {

            // Người dùng trả lời “đúng”
            if (in_array(strtolower($msg), ['đúng', 'đúng rồi', 'ok', 'oke', 'phải'])) {

                $id = session('pending_order_check');
                session()->forget('pending_order_check');

                // Ví dụ bạn tự xử lý DB Order
                return response()->json([
                    'assistant' => "📦 Trạng thái đơn *#$id*:  
                    👉 Đơn hàng đang được xử lý và chuẩn bị giao."
                ]);
            }

            // Người dùng trả lời sai
            if (in_array(strtolower($msg), ['sai', 'sai rồi', 'không'])) {
                session()->forget('pending_order_check');
                return response()->json([
                    'assistant' => "❗ Vui lòng gửi lại mã đơn dạng *#1234*."
                ]);
            }
        }
        if (Str::contains(Str::lower($msg), ['sản phẩm', 'bán gì', 'gợi ý', 'đề xuất', 'sp', 'serum', 'kem', 'dưỡng', 'trị mụn'])) {

    $products = \App\Models\Product::where('status', 1)
                ->orderByDesc('is_featured')
                ->take(5)
                ->get(['product_name','price','discount_price','capacity']);

    if ($products->isEmpty()) {
        return response()->json([
            'assistant' => "Hiện tại cửa hàng chưa cập nhật sản phẩm nào."
        ]);
    }

    $reply = "🎁 *Gợi ý một số sản phẩm tại cửa hàng:*\n\n";

    foreach ($products as $p) {
        $gia = $p->discount_price ?: $p->price;
        $reply .= "▫️ *{$p->product_name}*\n";
        $reply .= "   💰 Giá: ".number_format($gia,0,',','.')."₫\n";
        if ($p->capacity) {
            $reply .= "   ⚖ Dung tích: {$p->capacity}\n";
        }
        $reply .= "\n";
    }

    $reply .= "Bạn muốn xem chi tiết sản phẩm nào không ạ? 😊";

    return response()->json(['assistant' => $reply]);
}
$keywords = explode(' ', Str::lower($msg));

$productQuery = \App\Models\Product::query();

foreach ($keywords as $kw) {
    // Bỏ những từ vô nghĩa
    if (in_array($kw, ['là', 'với', 'loại', 'cái', 'sản', 'phẩm', 'nào', 'gì'])) {
        continue;
    }
    $productQuery->where('product_name', 'LIKE', "%{$kw}%");
}

$found = $productQuery->take(3)->get();

if ($found->count() > 0) {

    $reply = "🛍️ *Mình tìm được sản phẩm bạn quan tâm:*\n\n";

    foreach ($found as $p) {
        $gia = $p->discount_price ?: $p->price;

        $reply .= "▫️ *{$p->product_name}*\n";
        $reply .= "   💰 Giá: ".number_format($gia,0,',','.')."₫\n";
        if ($p->capacity) {
            $reply .= "   ⚖ Dung tích: {$p->capacity}\n";
        }
        $reply .= "\n";
    }

    return response()->json(['assistant' => $reply]);
}
if (preg_match('/(đặt hàng|mua hàng|mua|mua sản phẩm|làm sao mua|cách mua)/ui', $msg)) {

    return response()->json([
        'assistant' =>
            "🛒 *Bạn muốn đặt hàng đúng không ạ?*  
Bạn có 2 cách để mua hàng:

1️⃣ **Mua trực tiếp trên website**  
- Chọn sản phẩm  
- Nhấn *Thêm vào giỏ hàng*  
- Điền thông tin giao hàng  
- Chọn phương thức thanh toán (Momo / VNPAY / COD)  
- Xác nhận đơn hàng

2️⃣ **Đặt hàng qua Chatbot**  
Bạn chỉ cần gửi:  
👉 *Tên sản phẩm + Số lượng + Số điện thoại + Địa chỉ*  
Mình tạo đơn giúp bạn ngay! ❤️"
    ]);
}

        $ai = app(GeminiService::class)->generateText($msg);
        return response()->json(['assistant' => $ai]);
    }
}
