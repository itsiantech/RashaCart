<?php

namespace App\Services\AI;

use App\Models\AiUsageLog;
use App\Services\MaskPii;

/**
 * Single entry point for AI calls. Enforces the budget guard and logs usage.
 * Cost model: flat per-1K-token rate until provider billing lands.
 */
class AiService
{
    protected const COST_PER_1K = 0.002; // USD-ish units, configurable later

    /** Decrypted API key (null when unset/undecryptable). */
    public static function apiKey(): ?string
    {
        $enc = setting('automation_api_key');
        if (! $enc) {
            return null;
        }
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($enc);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function provider(): AiProvider
    {
        return match (setting('automation_provider', 'claude')) {
            default => new ClaudeProvider(),
        };
    }

    public static function budgetLeft(): array
    {
        $dailyLimit = (float) (setting('automation_daily_budget') ?? 500);
        $monthlyLimit = (float) (setting('automation_monthly_budget') ?? 5000);
        $today = (float) AiUsageLog::whereDate('created_at', today())->sum('cost');
        $month = (float) AiUsageLog::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->sum('cost');

        return [
            'today' => $today, 'daily_limit' => $dailyLimit,
            'month' => $month, 'monthly_limit' => $monthlyLimit,
            'blocked' => ($dailyLimit > 0 && $today >= $dailyLimit)
                || ($monthlyLimit > 0 && $month >= $monthlyLimit),
        ];
    }

    /** @throws \RuntimeException when budget is exhausted */
    public static function complete(string $moduleKey, string $prompt, array $options = []): array
    {
        if (self::budgetLeft()['blocked']) {
            AiUsageLog::create([
                'module_key' => $moduleKey, 'tokens_in' => 0, 'tokens_out' => 0,
                'cost' => 0, 'status' => 'blocked', 'created_at' => now(),
            ]);
            throw new \RuntimeException('AI বাজেট শেষ — call block করা হয়েছে।');
        }

        $result = self::provider()->complete(MaskPii::mask($prompt) ?? '', $options);
        $cost = (($result['tokens_in'] + $result['tokens_out']) / 1000) * self::COST_PER_1K;

        AiUsageLog::create([
            'module_key' => $moduleKey,
            'tokens_in' => $result['tokens_in'],
            'tokens_out' => $result['tokens_out'],
            'cost' => $cost,
            'status' => 'success',
            'created_at' => now(),
        ]);

        return $result + ['cost' => $cost];
    }
}
