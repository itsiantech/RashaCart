<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

/** Claude adapter skeleton — full chat logic lands with the first AI module. */
class ClaudeProvider implements AiProvider
{
    public function name(): string
    {
        return 'claude';
    }

    public function complete(string $prompt, array $options = []): array
    {
        $key = \App\Services\AI\AiService::apiKey();
        $model = $options['model'] ?? setting('automation_model', 'claude-haiku-4-5');
        $timeout = (int) (setting('automation_request_timeout') ?? 30);

        if (! $key) {
            throw new \RuntimeException('AI API key সেট করা নেই।');
        }

        $response = Http::timeout($timeout)->withHeaders([
            'x-api-key' => $key,
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $model,
            'max_tokens' => $options['max_tokens'] ?? 1024,
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Claude error: '.$response->status());
        }

        $data = $response->json();

        return [
            'text' => $data['content'][0]['text'] ?? '',
            'tokens_in' => $data['usage']['input_tokens'] ?? 0,
            'tokens_out' => $data['usage']['output_tokens'] ?? 0,
        ];
    }

    public function testConnection(): array
    {
        try {
            $result = $this->complete('Reply with: ok', ['max_tokens' => 10]);

            return ['success' => true, 'message' => 'Connected. ('.$result['tokens_in'].' in / '.$result['tokens_out'].' out tokens)'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
