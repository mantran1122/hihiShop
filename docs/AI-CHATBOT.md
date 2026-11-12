# Chatbot tự huấn luyện (không dùng LLM)

Chatbot của HIHI Shop **không gọi API LLM bên ngoài**. Nó học từ chính các cuộc hội thoại và phản hồi của khách:

```
Khách hỏi
   │
   ├─ Có câu trả lời tương tự trong bộ nhớ?  ── có ──► trả lời lại (hits +1)
   │                                            │
   └─ chưa có ─► sinh câu trả lời (rule + dữ liệu CSDL)
                  │
                  ├─ lưu cặp hỏi–đáp vào ai_training_pairs (quality 0.50)
                  ├─ khách bấm 👍/👎  ─► cập nhật quality_score
                  │        👎 x3 và quality < 0.35 ─► tự tắt (is_active = false)
                  └─ cập nhật hồ sơ sở thích user_ai_profiles
```

## Các bảng liên quan

| Bảng | Vai trò |
|---|---|
| `chat_sessions` | Phiên chat (lưu theo cookie 30 ngày) |
| `chat_messages` | Nội dung tin nhắn; `training_pair_id` nối câu trả lời với cặp đã học; `feedback` = 👍/👎 |
| `ai_training_pairs` | Bộ nhớ hỏi–đáp: `prompt_hash` (chống trùng), `hits`, `helpful_count`, `unhelpful_count`, `quality_score`, `is_active` |
| `user_activities` | Hành vi: `chat_message`, `product_view` (néu có `product_id` để tính sở thích) |
| `user_ai_profiles` | Hồ sơ suy ra: danh mục quan tâm, ngân sách, số tương tác, thời điểm phân tích |

## Cơ chế chọn câu trả lời (ChatbotLearningService)

1. Chuẩn hoá câu hỏi (lowercase, bỏ dấu câu), tách token.
2. Lấy tối đa 300 cặp đang hoạt động có `quality_score >= 0.40`.
3. So khớp Jaccard: `shared / union`, cần **>= 2 từ trùng** và **điểm >= 0.55**.
4. Khớp → trả lời bằng `ideal_response` đã lưu, tăng `hits`, kèm lại danh sách sản phẩm trong `context_json`.
5. Không khớp → sinh câu trả lời mới từ rule + dữ liệu CSDL rồi ghi vào bộ nhớ (mỗi câu hỏi chỉ một cặp nhờ `prompt_hash`).

Điểm chất lượng dùng Laplace smoothing: `(helpful + 1) / (helpful + unhelpful + 2)` — cặp mới bắt đầu 0.50, một 👍 thành 0.67, ba 👎 liên tiếp còn 0.20 và bị tắt.

## API

| Method | Endpoint | Mô tả |
|---|---|---|
| POST | `/chat/message` | Gửi câu hỏi; trả về `reply`, `products`, `message_id`, `learned` |
| POST | `/chat/feedback` | `{message_id, feedback: thumbs_up|thumbs_down}` — chỉ được đánh giá tin nhắn của phiên hiện tại |
| GET | `/chat/history` | 50 tin nhắn gần nhất (kèm `feedback` để UI hiển thị lại) |

## Kiểm tra / vận hành

```powershell
php artisan hihi:db-check          # thấy số cặp huấn luyện đang hoạt động
php artisan tinker                  # xem nhanh chất lượng bộ nhớ
>>> App\Models\AiTrainingPair::orderByDesc('quality_score')->take(10)->get(['prompt','hits','quality_score','is_active']);
```

Xuất dữ liệu để fine-tune về sau (nếu chuyển sang mô hình riêng):

```php
App\Models\AiTrainingPair::where('is_active', true)->where('quality_score', '>=', 0.7)
    ->get(['prompt','ideal_response'])->toJson(JSON_UNESCAPED_UNICODE);
```

## Giới hạn hiện tại

- So khớp theo **từ khoá**, chưa hiểu ngữ nghĩa/đồng nghĩa ("ship" vs "vận chuyển" chỉ khớp nếu trùng từ).
- Ngưỡng 0.55 và 2 từ trùng có thể chỉnh trong `ChatbotLearningService` (`MATCH_THRESHOLD`, `MIN_SHARED_TOKENS`).
- Không có hàng đợi huấn luyện nền; việc học diễn ra ngay trong request chat (chi phí rất nhỏ vì chỉ đọc tối đa 300 cặp).
- Hồ sơ AI cập nhật 200 hoạt động gần nhất mỗi lần chat (nếu dữ liệu lớn nên chuyển sang queue/cron).
