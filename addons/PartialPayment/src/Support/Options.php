<?php

namespace Addons\PartialPayment\Support;

class Options
{
    public const METHODS = [
        'bkash' => 'bKash',
        'nagad' => 'Nagad',
        'rocket' => 'Rocket',
    ];

    public static function base(): string
    {
        return (string) setting('pp_base', 'total') === 'subtotal' ? 'subtotal' : 'total';
    }

    public static function expiryHours(): int
    {
        return max(0, (int) setting('pp_expiry_hours', 24));
    }

    public static function number(string $method): string
    {
        return trim((string) setting('pp_'.$method.'_number', ''));
    }

    public static function instructions(): string
    {
        return (string) setting('pp_instructions', '');
    }

    /** Methods a customer can actually pay to (merchant number filled in). */
    public static function availableMethods(): array
    {
        $ready = [];

        foreach (self::METHODS as $key => $label) {
            if (self::number($key) !== '') {
                $ready[$key] = $label;
            }
        }

        return $ready;
    }

    public static function defaults(): array
    {
        return [
            'pp_base' => 'total',
            'pp_expiry_hours' => '24',
            'pp_bkash_number' => '',
            'pp_nagad_number' => '',
            'pp_rocket_number' => '',
            'pp_instructions' => 'অ্যাডভান্সটুকু নাম্বারে বিকাশ/নগদ/রকেট করে TrxID দিন। অর্ডার কনফার্ম হলে বাকি টাকা ক্যাশ অন ডেলিভারি দিবেন।',
        ];
    }
}
