<?php

namespace Tests\Unit;

use App\Services\Tracking\HashingService;
use PHPUnit\Framework\TestCase;

class TrackingHashingTest extends TestCase
{
    protected HashingService $hashing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hashing = new HashingService();
    }

    public function test_email_is_lowercased_trimmed_and_hashed(): void
    {
        $this->assertSame(hash('sha256', 'admin@gmail.com'), $this->hashing->hashEmail('  Admin@Gmail.com '));
        $this->assertNull($this->hashing->hashEmail(null));
        $this->assertNull($this->hashing->hashEmail(''));
    }

    public function test_bd_phone_variants_normalize_to_880_format(): void
    {
        $expected = hash('sha256', '8801712345678');

        $this->assertSame($expected, $this->hashing->hashPhone('01712345678'));
        $this->assertSame($expected, $this->hashing->hashPhone('+8801712345678'));
        $this->assertSame($expected, $this->hashing->hashPhone('8801712345678'));
        $this->assertSame($expected, $this->hashing->hashPhone('017-1234-5678'));
    }

    public function test_invalid_phones_return_null(): void
    {
        $this->assertNull($this->hashing->hashPhone(null));
        $this->assertNull($this->hashing->hashPhone(''));
        $this->assertNull($this->hashing->hashPhone('12345'));
        $this->assertNull($this->hashing->hashPhone('02712345678')); // landline, not 8801
    }

    public function test_purchase_event_id_is_deterministic(): void
    {
        $this->assertSame('purchase_42', $this->hashing->generatePurchaseEventId(42));
        $this->assertSame(
            $this->hashing->generatePurchaseEventId(42),
            $this->hashing->generatePurchaseEventId(42)
        );
    }

    public function test_generated_event_ids_are_unique_uuids(): void
    {
        $a = $this->hashing->generateEventId();
        $b = $this->hashing->generateEventId();

        $this->assertNotSame($a, $b);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $a
        );
    }
}
