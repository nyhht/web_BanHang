<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function fetchMessages(Request $request)
    {
        if (Auth::check()) {
            $msgs = ChatMessage::where('user_id', Auth::id())->orderBy('created_at')->get();
        } else {
            $token = $request->cookie('chat_token');
            $msgs = $token ? ChatMessage::where('guest_token', $token)->orderBy('created_at')->get() : collect();
        }

        return response()->json($msgs);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userId = Auth::id();
        $guestToken = null;

        if (!$userId) {
            $guestToken = $request->cookie('chat_token');
            if (!$guestToken) {
                $guestToken = 'guest_' . Str::random(32);
                cookie()->queue(cookie('chat_token', $guestToken, 60 * 24 * 180));
            }
        }

        $userMsg = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'user',
            'message' => $request->message,
        ]);

        $products = $this->availableProducts();
        $contents = $this->buildGeminiContents($userId, $guestToken);
        $prompt = $this->buildGeminiPrompt($products);

        $aiReplyText = $this->buildLocalFallbackReply($request->message, $products);
        $geminiApiKey = env('GOOGLE_GEMINI_API_KEY');

        if ($geminiApiKey) {
            $aiReplyText = $this->generateGeminiReply($geminiApiKey, $prompt, $contents) ?? $aiReplyText;
        }

        $botMsg = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'bot',
            'message' => $aiReplyText,
        ]);

        return response()->json([
            'user' => $userMsg,
            'bot' => $botMsg,
        ]);
    }

    private function availableProducts(): Collection
    {
        return Product::with('category')
            ->where('status', 'in_stock')
            ->where('stock', '>', 0)
            ->get([
                'id',
                'name',
                'category_id',
                'description',
                'price',
                'unit',
                'stock',
                'status',
            ]);
    }

    private function buildGeminiPrompt(Collection $products): string
    {
        $productList = $products->take(30)->map(function (Product $product) {
            $category = $product->category?->name ? " - {$product->category->name}" : '';
            $unit = $product->unit ? " / {$product->unit}" : '';
            $duration = $product->prep_time || $product->cook_time
                ? ' - khoảng ' . ((int) $product->prep_time + (int) $product->cook_time) . ' phút'
                : '';

            return "{$product->name}{$category} - {$product->current_price}{$unit}{$duration}";
        })->implode("\n");

        return "Bạn là trợ lý bán hàng cho mealkit. Dưới đây là danh sách một số sản phẩm hiện có:\n{$productList}\n"
            . "Hãy trả lời ngắn gọn, trung thực, chỉ dùng thông tin trong danh sách sản phẩm nếu cần.";
    }

    private function buildGeminiContents(?int $userId, ?string $guestToken): array
    {
        $history = ChatMessage::query()
            ->where(function ($query) use ($userId, $guestToken) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('guest_token', $guestToken);
                }
            })
            ->latest()
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        return $history->map(function (ChatMessage $message) {
            return [
                'role' => $message->sender === 'user' ? 'user' : 'model',
                'parts' => [
                    ['text' => $message->message],
                ],
            ];
        })->all();
    }

    private function generateGeminiReply(string $apiKey, string $prompt, array $contents): ?string
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . rawurlencode($apiKey);
        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $prompt],
                ],
            ],
            'contents' => $contents,
        ];

        $response = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::timeout(20)
                    ->acceptJson()
                    ->asJson()
                    ->withHeaders([
                        'X-Goog-Api-Key' => $apiKey,
                    ])
                    ->post($url, $payload);

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');

                    return is_string($text) && trim($text) !== '' ? trim($text) : null;
                }

                if (!in_array($response->status(), [429, 503], true)) {
                    break;
                }

                if ($attempt < 3) {
                    usleep(250000 * $attempt);
                }
            } catch (\Throwable $exception) {
                Log::warning('AI call error', ['message' => $exception->getMessage()]);

                return null;
            }
        }

        Log::warning('AI API unavailable', [
            'status' => $response?->status(),
            'response' => $response?->json(),
        ]);

        return null;
    }

    private function buildLocalFallbackReply(string $message, Collection $products): string
    {
        if ($products->isEmpty()) {
            return 'Mình chưa thấy món còn hàng để gợi ý. Bạn vui lòng thử lại sau nhé.';
        }

        $normalizedMessage = Str::lower(Str::ascii($message));
        $intentKeywords = ['ngon', 'de an', 'phu hop'];

        if (
            str_contains($normalizedMessage, 'mua he')
            || str_contains($normalizedMessage, 'troi nong')
            || str_contains($normalizedMessage, 'nong')
            || str_contains($normalizedMessage, 'mat')
            || str_contains($normalizedMessage, 'thoai mai')
        ) {
            $intentKeywords = ['eat clean', 'rau', 'cuon', 'bun', 'canh', 'ca', 'tom', 'ga', 'nhanh', '15 phut', 'nhe', 'mat', 'thanh'];
        } elseif (str_contains($normalizedMessage, 'nhanh') || str_contains($normalizedMessage, 'ban')) {
            $intentKeywords = ['nhanh', '15 phut', 'de nau', 'toi gian'];
        } elseif (str_contains($normalizedMessage, 're') || str_contains($normalizedMessage, 'tiet kiem')) {
            $intentKeywords = ['combo', 'tiet kiem', 'gia dinh'];
        } elseif (str_contains($normalizedMessage, 'gia dinh') || str_contains($normalizedMessage, 'nhieu nguoi')) {
            $intentKeywords = ['gia dinh', 'combo', 'lau', 'nuong'];
        }

        $suggestions = $products
            ->map(function (Product $product) use ($intentKeywords) {
                $haystack = Str::lower(Str::ascii(implode(' ', [
                    $product->name,
                    $product->description,
                    $product->category?->name,
                    $product->prep_time !== null && $product->prep_time <= 15 ? '15 phut nhanh' : '',
                    $product->calories !== null && $product->calories <= 500 ? 'nhe eat clean' : '',
                ])));

                $score = 0;
                foreach ($intentKeywords as $keyword) {
                    if (str_contains($haystack, $keyword)) {
                        $score += 2;
                    }
                }

                if ($product->prep_time !== null && $product->prep_time <= 15) {
                    $score++;
                }

                if ($product->calories !== null && $product->calories <= 500) {
                    $score++;
                }

                return [
                    'product' => $product,
                    'score' => $score,
                ];
            })
            ->sortByDesc('score')
            ->take(3)
            ->pluck('product');

        $lines = $suggestions->map(function (Product $product) {
            $price = number_format($product->current_price, 0, ',', '.') . 'VNĐ';
            $unit = $product->unit ? " / {$product->unit}" : '';
            $duration = $product->prep_time || $product->cook_time
                ? ' khoảng ' . ((int) $product->prep_time + (int) $product->cook_time) . ' phút'
                : '';

            return "- {$product->name}: {$price}{$unit}{$duration}";
        })->implode("\n");

        return "Mình gợi ý nhanh từ menu còn hàng nhé:\n{$lines}\nCác món này hợp khi trời nóng vì ưu tiên món nhẹ, dễ ăn và không mất nhiều thời gian nấu.";
    }
}
