<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        try {
            $response = Http::timeout(60)->withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'user', 'content' => $request->message],
                ],
                'temperature' => 0.3,
                'max_tokens' => 100,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Ошибка от OpenAI API',
                    'details' => $response->body(),
                ], $response->status());
            }

            return response()->json([
                'reply' => $response->json('choices.0.message.content') ?? 'Нет ответа',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Серверная ошибка: ' . $e->getMessage(),
            ], 500);
        }
    }
}
