<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function validateAndApply($code, $cartItems)
    {
        if (!$code) {
            return ['error' => 'Please enter a coupon code'];
        }

        $coupon = Coupon::where('code', $code)->where('status', 1)->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->first();

        if (!$coupon) {
            return ['error' => 'Invalid or expired coupon'];
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return ['error' => 'This coupon has reached its usage limit'];
        }

        $eligibleAmount = $this->eligibleSubtotal($coupon, $cartItems);

        if ($eligibleAmount <= 0) {
            return ['error' => 'This coupon does not apply to your cart items'];
        }

        if ($coupon->min_order_amount && $eligibleAmount < $coupon->min_order_amount) {
            return ['error' => "Minimum order amount required is {$coupon->min_order_amount}"];
        }

        $discount = $this->calculateDiscount($coupon, $eligibleAmount);

        return [
            'success' => true,
            'discount' => $discount,
        ];
    }

    private function calculateDiscount($coupon, $amount)
    {
        if ($coupon->discount_type === 'percent') {
            $discount = ($amount * $coupon->discount_value) / 100;

            if ($coupon->max_discount_amount) {
                $discount = min($discount, $coupon->max_discount_amount);
            }
        } else {
            $discount = $coupon->discount_value;
        }

        return $discount;
    }

    private function eligibleSubtotal($coupon, $cartItems)
    {
        $productIds = $coupon->products->pluck('id')->toArray();
        $categoryIds = $coupon->categories->pluck('id')->toArray();

        $hasProductLimit = !empty($productIds);
        $hasCategoryLimit = !empty($categoryIds);

        return collect($cartItems)->sum(function ($item) use ($productIds, $categoryIds, $hasProductLimit, $hasCategoryLimit) {
            if (!$hasProductLimit && !$hasCategoryLimit) {
                return $item['price'] * $item['qty'];
            }

            if ($hasProductLimit && in_array($item['id'], $productIds)) {
                return $item['price'] * $item['qty'];
            }

            if ($hasCategoryLimit && isset($item['category_id']) && in_array($item['category_id'], $categoryIds)) {
                return $item['price'] * $item['qty'];
            }

            return 0;
        });
    }
}
