<?php

namespace Tests\Feature;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleHomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function makeCampaign(array $over = [], array $items = []): FlashSale
    {
        $sale = FlashSale::create(array_merge([
            'title' => 'Mega Sale',
            'start_at' => now()->subHour(),
            'end_at' => now()->addDays(2),
            'is_active' => true,
        ], $over));

        foreach ($items as $productId => $pct) {
            FlashSaleItem::create([
                'flash_sale_id' => $sale->id,
                'product_id' => $productId,
                'discount_percent' => $pct,
                'discounted_price' => 100,
                'is_active' => true,
            ]);
        }

        return $sale;
    }

    public function test_live_campaign_shows_with_end_at_countdown(): void
    {
        $p = \App\Models\Product::factory()->create(['status' => true]);
        $sale = $this->makeCampaign(['end_at' => now()->addDays(2)->addHours(3)], [$p->id => 20]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString($p->name, $html);
        // Countdown targets the campaign end, not end-of-day
        $this->assertStringContainsString((string) ($sale->end_at->timestamp * 1000), $html);
        // Server-rendered initial boxes (not hardcoded 00)
        $this->assertStringContainsString('id="cdDays">02<', $html);
    }

    public function test_no_live_campaign_hides_section_entirely(): void
    {
        // Discounted product exists but NO campaign → section must not appear
        \App\Models\Product::factory()->create([
            'status' => true, 'price' => 1000, 'sale_price' => 800,
            'discount_type' => 'percentage', 'discount_price' => 20,
        ]);
        // Ended campaign → hidden too
        $p2 = \App\Models\Product::factory()->create(['status' => true]);
        $this->makeCampaign(
            ['start_at' => now()->subDays(5), 'end_at' => now()->subDays(1)],
            [$p2->id => 10]
        );

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('Limited Time Discount Offer', $html);
    }

    public function test_overlapping_campaigns_latest_wins(): void
    {
        $old = \App\Models\Product::factory()->create(['name' => 'Old Campaign Tee', 'status' => true]);
        $new = \App\Models\Product::factory()->create(['name' => 'New Campaign Tee', 'status' => true]);

        $this->makeCampaign(['title' => 'First', 'start_at' => now()->subDays(2)], [$old->id => 10]);
        FlashSale::where('title', 'First')->update(['created_at' => now()->subDay()]);
        $this->makeCampaign(['title' => 'Second', 'start_at' => now()->subHour()], [$new->id => 15]);

        $html = $this->get('/')->assertOk()->getContent();

        // Scope to the flash section markup (products also appear in Recommended, etc.)
        $start = strpos($html, '<div class="hp-deal-products">');
        $this->assertNotFalse($start);
        $end = strpos($html, 'Recommended', $start);
        $section = substr($html, $start, ($end ?: $start + 6000) - $start);

        $this->assertStringContainsString('New Campaign Tee', $section);
        $this->assertStringNotContainsString('Old Campaign Tee', $section);
    }
}
