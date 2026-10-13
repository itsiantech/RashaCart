<?php

namespace Tests\Unit;

use App\Services\VariantGenerator;
use PHPUnit\Framework\TestCase;

class VariantGeneratorTest extends TestCase
{
    protected VariantGenerator $gen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gen = new VariantGenerator();
    }

    public function test_combinations_build_cartesian_product(): void
    {
        $combos = $this->gen->combinations([1 => [10, 11], 2 => [20, 21, 22]]);

        $this->assertCount(6, $combos);
        $this->assertContains([1 => 10, 2 => 20], $combos);
        $this->assertContains([1 => 11, 2 => 22], $combos);
    }

    public function test_empty_selection_gives_no_combos(): void
    {
        $this->assertSame([], $this->gen->combinations([]));
        $this->assertSame([], $this->gen->combinations([1 => []]));
    }

    public function test_combo_key_is_order_stable(): void
    {
        $this->assertSame(
            $this->gen->comboKey([2 => 21, 1 => 10]),
            $this->gen->comboKey([1 => 10, 2 => 21])
        );
        $this->assertNotSame(
            $this->gen->comboKey([1 => 10]),
            $this->gen->comboKey([1 => 11])
        );
    }
}
