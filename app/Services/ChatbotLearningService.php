<?php

namespace App\Services;

use App\Models\AiTrainingPair;
use App\Models\ChatMessage;
use App\Models\SanPham;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserAiProfile;

/**
 * Vòng "tự huấn luyện" của chatbot — KHÔNG dùng LLM bên ngoài.
 *
 * Cơ chế:
 *  1. Mỗi câu trả lời của bot được lưu thành một cặp (prompt → response) trong
 *     ai_training_pairs, kèm chất lượng mặc định 0.5.
 *  2. Lần sau, câu hỏi tương tự sẽ được trả lời lại từ bộ nhớ (similarity token),
 *     không cần chạy lại rule.
 *  3. Phản hồi 👍/👎 của khách cập nhật quality_score; cặp bị 👎 nhiều sẽ tự tắt.
 *  4. Hành vi xem sản phẩm + chat được tổng hợp vào user_ai_profiles để cá nhân hoá.
 */
class ChatbotLearningService
{
    /** Ngưỡng tương đồng tối thiểu để dùng lại câu trả lời đã học. */
    private const MATCH_THRESHOLD = 0.55;

    /** Số từ tối thiểu phải trùng nhau. */
    private const MIN_SHARED_TOKENS = 2;

    /**
     * Chuẩn hoá câu hỏi: lowercase, bỏ dấu câu, gộp khoảng trắng.
     */
    public function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }

    /**
     * Tách token (bỏ từ 1 ký tự).
     *
     * @return array<int, string>
     */
    public function tokens(string $text): array
    {
        return array_values(array_unique(array_filter(
            explode(' ', $this->normalize($text)),
            fn (string $word) => mb_strlen($word) >= 2
        )));
    }

    /**
     * Tìm câu trả lời đã học cho câu hỏi hiện tại.
     *
     * @return array{pair: AiTrainingPair, score: float, answer: string}|null
     */
    public function findLearnedAnswer(string $prompt): ?array
    {
        $tokens = $this->tokens($prompt);

        if (count($tokens) === 0) {
            return null;
        }

        $candidates = AiTrainingPair::query()
            ->where('is_active', true)
            ->where('quality_score', '>=', 0.40)
            ->orderByDesc('quality_score')
            ->orderByDesc('hits')
            ->limit(300)
            ->get();

        $best = null;
        $bestScore = 0.0;

        foreach ($candidates as $pair) {
            $pairTokens = $this->tokens((string) $pair->prompt);

            if (count($pairTokens) === 0) {
                continue;
            }

            $shared = count(array_intersect($tokens, $pairTokens));

            if ($shared < self::MIN_SHARED_TOKENS) {
                continue;
            }

            $union = count(array_unique(array_merge($tokens, $pairTokens)));
            $score = $union > 0 ? $shared / $union : 0.0;

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $pair;
            }
        }

        if ($best === null || $bestScore < self::MATCH_THRESHOLD) {
            return null;
        }

        $best->increment('hits');

        return [
            'pair' => $best,
            'score' => round($bestScore, 3),
            'answer' => $best->ideal_response,
        ];
    }

    /**
     * Ghi nhớ (hoặc cập nhật) một cặp hỏi–đáp để tái sử dụng về sau.
     */
    public function remember(string $prompt, string $response, array $context = [], string $source = 'rule_based'): AiTrainingPair
    {
        $hash = sha1($this->normalize($prompt));

        $pair = AiTrainingPair::firstOrNew(['prompt_hash' => $hash]);

        if (!$pair->exists) {
            $pair->prompt = $prompt;
            $pair->ideal_response = $response;
            $pair->context_json = $context;
            $pair->source = $source;
            $pair->quality_score = 0.50;
            $pair->is_active = true;
            $pair->save();
        }

        return $pair;
    }

    /**
     * Hydrate danh sách sản phẩm đã lưu trong ngữ cảnh của cặp hỏi–đáp.
     *
     * @return array<int, array<string, mixed>>
     */
    public function productsForPair(AiTrainingPair $pair): array
    {
        $ids = $pair->context_json['product_ids'] ?? [];

        if (!is_array($ids) || $ids === []) {
            return [];
        }

        return SanPham::query()
            ->whereIn('id', $ids)
            ->get()
            ->map(fn (SanPham $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format((float) $product->price, 0, ',', '.').' ₫',
                'image' => $product->image_url,
                'url' => route('product.show', $product->slug),
                'category' => $product->danhMuc?->name ?? 'Phụ kiện',
            ])
            ->values()
            ->all();
    }

    /**
     * Cập nhật điểm chất lượng từ phản hồi 👍/👎 của khách.
     */
    public function recordFeedback(ChatMessage $message, string $feedback): void
    {
        $message->update(['feedback' => $feedback]);

        if (!$message->training_pair_id) {
            return;
        }

        $pair = AiTrainingPair::find($message->training_pair_id);

        if (!$pair) {
            return;
        }

        if ($feedback === 'thumbs_up') {
            $pair->increment('helpful_count');
        } elseif ($feedback === 'thumbs_down') {
            $pair->increment('unhelpful_count');
        } else {
            return;
        }

        $pair->refresh();

        $helpful = (int) $pair->helpful_count;
        $unhelpful = (int) $pair->unhelpful_count;

        // Laplace smoothing để cặp mới không bị điểm tuyệt đối.
        $pair->quality_score = round(($helpful + 1) / ($helpful + $unhelpful + 2), 2);

        // Bị chê nhiều và điểm thấp → tự tắt, không dùng lại nữa.
        if ($unhelpful >= 3 && $pair->quality_score < 0.35) {
            $pair->is_active = false;
        }

        $pair->save();
    }

    /**
     * Tổng hợp hành vi khách hàng thành hồ sơ AI (sở thích, ngân sách, mức tương tác).
     */
    public function refreshProfile(?User $user, ?string $sessionId = null): void
    {
        if (!$user) {
            return;
        }

        $activities = UserActivity::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(200)
            ->get();

        $categoryCounts = [];
        $prices = [];

        foreach ($activities as $activity) {
            $payload = $activity->payload ?? [];
            $productId = $payload['product_id'] ?? null;

            if (!$productId) {
                continue;
            }

            $product = SanPham::with('danhMuc')->find($productId);

            if (!$product) {
                continue;
            }

            $categoryName = $product->danhMuc?->name ?? 'Khác';
            $categoryCounts[$categoryName] = ($categoryCounts[$categoryName] ?? 0) + 1;

            if ((float) $product->price > 0) {
                $prices[] = (float) $product->price;
            }
        }

        arsort($categoryCounts);
        $topCategories = array_slice(array_keys($categoryCounts), 0, 3);
        $averagePrice = $prices !== [] ? array_sum($prices) / count($prices) : 0.0;

        $budget = match (true) {
            $averagePrice <= 0 => 'undecided',
            $averagePrice < 500000 => 'budget',
            $averagePrice < 2000000 => 'mid-range',
            default => 'luxury',
        };

        UserAiProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_categories' => $topCategories,
                'budget_range' => $budget,
                'style_persona' => $topCategories !== []
                    ? 'Quan tâm '.$topCategories[0]
                    : 'Khách hàng mới',
                'interaction_count' => $activities->count(),
                'last_analyzed_at' => now(),
            ]
        );
    }
}
