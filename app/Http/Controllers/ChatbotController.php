<?php

namespace App\Http\Controllers;

use App\Models\AiTrainingPair;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\SanPham;
use App\Models\UserActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    /**
     * Get or initialize chat session ID.
     */
    protected function getSessionId(Request $request): string
    {
        $sessionId = $request->cookie('hihi_chat_session') ?: $request->session()->get('hihi_chat_session');
        if (!$sessionId) {
            $sessionId = 'sess_' . Str::random(24);
            $request->session()->put('hihi_chat_session', $sessionId);
        }

        ChatSession::firstOrCreate(
            ['id' => $sessionId],
            [
                'user_id' => Auth::id(),
                'session_title' => 'Tư vấn AI HIHI Shop',
                'started_at' => now(),
            ]
        );

        return $sessionId;
    }

    /**
     * Get chat history for current session.
     */
    public function getHistory(Request $request): JsonResponse
    {
        $sessionId = $this->getSessionId($request);
        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('id', 'asc')
            ->take(50)
            ->get();

        return response()->json([
            'session_id' => $sessionId,
            'messages' => $messages,
        ])->cookie('hihi_chat_session', $sessionId, 60 * 24 * 30);
    }

    /**
     * Send message to AI Chatbot.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $sessionId = $this->getSessionId($request);
        $userText = trim($request->input('message'));

        // 1. Save user message
        ChatMessage::create([
            'session_id' => $sessionId,
            'sender' => 'user',
            'content' => $userText,
            'created_at' => now(),
        ]);

        // 2. Track activity for AI RAG context
        UserActivity::create([
            'user_id' => Auth::id(),
            'session_id' => $sessionId,
            'event_type' => 'chat_message',
            'payload' => ['prompt' => $userText],
            'created_at' => now(),
        ]);

        // 3. Generate AI Intelligence Response (RAG + Rule-based intent detection)
        $aiResult = $this->generateAiResponse($userText);

        // 4. Save bot message
        ChatMessage::create([
            'session_id' => $sessionId,
            'sender' => 'bot',
            'content' => $aiResult['text'],
            'recommended_product_ids' => !empty($aiResult['product_ids']) ? $aiResult['product_ids'] : null,
            'created_at' => now(),
        ]);

        // 5. Save training pair candidate for model transfer learning
        AiTrainingPair::create([
            'prompt' => $userText,
            'ideal_response' => $aiResult['text'],
            'context_json' => [
                'user_id' => Auth::id(),
                'intent' => $aiResult['intent'],
            ],
            'source' => 'high_rated_session',
        ]);

        return response()->json([
            'status' => 'success',
            'session_id' => $sessionId,
            'reply' => $aiResult['text'],
            'products' => $aiResult['products'],
        ])->cookie('hihi_chat_session', $sessionId, 60 * 24 * 30);
    }

    /**
     * Generate context-aware AI response based on real-time database data.
     */
    protected function generateAiResponse(string $prompt): array
    {
        $normalized = mb_strtolower($prompt, 'UTF-8');
        $products = [];
        $productIds = [];
        $intent = 'general_consultation';

        // Intent: Login, Register, Account issues
        if (Str::contains($normalized, ['đăng nhập', 'login', 'đăng ký', 'register', 'tạo tài khoản', 'google', 'facebook', 'fb', 'mật khẩu', 'quên mật khẩu'])) {
            $intent = 'auth_support';
            if (Str::contains($normalized, ['google', 'facebook', 'fb', '1-click', 'nhanh'])) {
                $text = "Bạn có thể đăng nhập hoặc đăng ký tài khoản cực kỳ nhanh chóng bằng cách bấm vào biểu tượng **Google** hoặc **Facebook** ngay trên form Diprella. Hệ thống sẽ tự động đồng bộ tài khoản và tạo hồ sơ khách hàng ngay lập tức mà không cần gõ mật khẩu!";
            } elseif (Str::contains($normalized, ['quên', 'reset', 'lấy lại'])) {
                $text = "Để lấy lại mật khẩu, bạn chỉ cần bấm vào liên kết **'Quên mật khẩu?'** ngay bên dưới ô mật khẩu, sau đó nhập địa chỉ email đã đăng ký. Hệ thống HIHI Shop sẽ gửi ngay liên kết an toàn để bạn đặt lại mật khẩu mới.";
            } else {
                $text = "Để đăng ký thành viên mới tại **HIHI Shop**, bạn chỉ cần điền Họ tên, Email và Mật khẩu (tối thiểu 8 ký tự), hoặc chọn Đăng ký nhanh qua Google/Facebook. Thành viên mới sẽ nhận ngay các đặc quyền: lưu giỏ hàng, theo dõi đơn hàng và nhận ưu đãi tích điểm VIP!";
            }

            return [
                'text' => $text,
                'products' => [],
                'product_ids' => [],
                'intent' => $intent,
            ];
        }

        // Intent: Shipping, Return, Warranty policies
        if (Str::contains($normalized, ['giao hàng', 'vận chuyển', 'ship', 'đổi trả', 'bảo hành', 'chính hãng', 'hotline', 'liên hệ', 'phí ship'])) {
            $intent = 'policy_support';
            $text = "Dạ, **HIHI Shop** cam kết 100% sản phẩm là hàng chính hãng cao cấp:\n\n" .
                    "• **Vận chuyển:** Hỗ trợ giao hỏa tốc 2h tại khu vực nội thành, toàn quốc nhận hàng sau 1 - 3 ngày.\n" .
                    "• **Đổi trả:** Miễn phí đổi trả trong 7 ngày nếu có lỗi từ nhà sản xuất.\n" .
                    "• **Bảo hành:** Bảo hành chính hãng 12 tháng với đồng hồ và mắt kính.\n" .
                    "• **Hotline hỗ trợ:** 1900 0129 (8:00 - 22:00 mỗi ngày).";

            return [
                'text' => $text,
                'products' => [],
                'product_ids' => [],
                'intent' => $intent,
            ];
        }

        // Intent: Product inquiries (Nước hoa, Giày, Đồng hồ, Kính mắt, Sản phẩm hot)
        $query = SanPham::active()->with(['danhMuc', 'thuongHieu']);

        if (Str::contains($normalized, ['nước hoa', 'perfume', 'mùi hương'])) {
            $intent = 'product_perfume';
            $query->whereHas('danhMuc', function ($q) {
                $q->where('slug', 'like', '%nuoc-hoa%');
            })->orWhere('name', 'like', '%nước hoa%');
        } elseif (Str::contains($normalized, ['giày', 'sneaker', 'dép'])) {
            $intent = 'product_shoes';
            $query->whereHas('danhMuc', function ($q) {
                $q->where('slug', 'like', '%giay%');
            })->orWhere('name', 'like', '%giày%');
        } elseif (Str::contains($normalized, ['đồng hồ', 'watch'])) {
            $intent = 'product_watch';
            $query->whereHas('danhMuc', function ($q) {
                $q->where('slug', 'like', '%dong-ho%');
            })->orWhere('name', 'like', '%đồng hồ%');
        } elseif (Str::contains($normalized, ['kính', 'mắt kính', 'sunglasses'])) {
            $intent = 'product_glasses';
            $query->whereHas('danhMuc', function ($q) {
                $q->where('slug', 'like', '%kinh%');
            })->orWhere('name', 'like', '%kính%');
        } else {
            // General keywords search
            $searchKeywords = array_filter(explode(' ', $normalized), fn($w) => mb_strlen($w) > 2);
            if (!empty($searchKeywords)) {
                $query->where(function ($q) use ($searchKeywords) {
                    foreach ($searchKeywords as $kw) {
                        $q->orWhere('name', 'like', "%{$kw}%")
                          ->orWhere('short_desc', 'like', "%{$kw}%");
                    }
                });
            }
        }

        $foundProducts = $query->take(3)->get();

        if ($foundProducts->isNotEmpty()) {
            foreach ($foundProducts as $p) {
                $productIds[] = $p->id;
                $products[] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => number_format($p->price, 0, ',', '.') . ' ₫',
                    'image' => $p->image_url,
                    'url' => route('product.show', $p->slug),
                    'category' => $p->danhMuc?->name ?? 'Phụ kiện',
                ];
            }

            $text = "Dựa trên kho dữ liệu thời gian thực của HIHI Shop, mình xin gợi ý cho bạn một số sản phẩm nổi bật phù hợp với nhu cầu:\n\n" .
                    "Bạn có thể bấm trực tiếp vào từng sản phẩm bên dưới để xem chi tiết thông số và ưu đãi nhé!";
        } else {
            $text = "Chào bạn! Mình là **HIHI AI Assistant** được huấn luyện theo thời gian thực từ CSDL HIHI Shop. Mình có thể giúp bạn:\n\n" .
                    "• Tư vấn chọn nước hoa, giày sneaker, đồng hồ và mắt kính chính hãng.\n" .
                    "• Hướng dẫn đăng ký, đăng nhập nhanh qua Google / Facebook.\n" .
                    "• Tra cứu chính sách bảo hành, đổi trả và giao hàng tận nơi.\n\n" .
                    "Bạn hãy gửi câu hỏi hoặc tên sản phẩm bạn quan tâm nhé!";
        }

        return [
            'text' => $text,
            'products' => $products,
            'product_ids' => $productIds,
            'intent' => $intent,
        ];
    }
}
