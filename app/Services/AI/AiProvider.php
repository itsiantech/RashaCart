<?php

namespace App\Services\AI;

/** Provider abstraction — modules call AiService, never providers directly. */
interface AiProvider
{
    public function name(): string;

    /** @return ['text' => string, 'tokens_in' => int, 'tokens_out' => int] */
    public function complete(string $prompt, array $options = []): array;

    public function testConnection(): array; // ['success' => bool, 'message' => string]
}
