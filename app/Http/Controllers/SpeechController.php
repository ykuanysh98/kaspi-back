<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SpeechController extends Controller
{
    public function transcribe(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,wav,mp3,mp4,mpeg',
        ]);

        $file = $request->file('audio');
        $path = $file->getPathname();
        $name = $file->getClientOriginalName();

        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->attach('file', fopen($path, 'r'), $name)
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model' => 'whisper-1',
                    'language' => 'ru', // Қазақша тану үшін
                ]);

            return response()->json([
                'text' => $response->json('text'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Whisper API қатесі: ' . $e->getMessage(),
            ], 500);
        }
    }
}
