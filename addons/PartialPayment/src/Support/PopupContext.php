<?php

namespace Addons\PartialPayment\Support;

use Addons\PartialPayment\Services\PlanCalculator;
use Livewire\Component;

class PopupContext
{
    /**
     * The advance plan for what the customer is looking at right now, computed
     * from the server-side price the popup already resolved. Null when the
     * product carries no rule or nothing would be left to collect.
     */
    public static function plan(Component $popup): ?array
    {
        if (empty($popup->productId)) {
            return null;
        }

        $plan = PlanCalculator::preview(
            (int) $popup->productId,
            (float) $popup->unitPrice,
            max(1, (int) $popup->qty),
            (float) $popup->deliveryCharge()
        );

        return $plan === [] ? null : $plan;
    }

    public static function requiresAdvance(Component $popup): bool
    {
        return self::plan($popup) !== null;
    }
}
