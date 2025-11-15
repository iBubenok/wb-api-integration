<?php

namespace Tests\Unit\Models;

use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Тесты модели Sale
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class SaleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест создания продажи
     */
    public function test_can_create_sale(): void
    {
        $sale = Sale::create([
            'external_id' => 'test_001',
            'sale_date' => '2024-01-15',
            'article' => 'ART123',
            'price' => 1000.00,
            'discount' => 100.00,
            'final_price' => 900.00,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('sales', [
            'external_id' => 'test_001',
            'article' => 'ART123',
        ]);

        $this->assertEquals('test_001', $sale->external_id);
        $this->assertEquals('ART123', $sale->article);
    }

    /**
     * Тест поиска по внешнему ID
     */
    public function test_can_find_by_external_id(): void
    {
        Sale::create([
            'external_id' => 'test_002',
            'sale_date' => '2024-01-15',
            'article' => 'ART124',
            'price' => 1500.00,
            'discount' => 0,
            'final_price' => 1500.00,
            'quantity' => 2,
        ]);

        $sale = Sale::findByExternalId('test_002');

        $this->assertNotNull($sale);
        $this->assertEquals('test_002', $sale->external_id);
    }

    /**
     * Тест проверки существования по внешнему ID
     */
    public function test_can_check_exists_by_external_id(): void
    {
        Sale::create([
            'external_id' => 'test_003',
            'sale_date' => '2024-01-15',
            'article' => 'ART125',
            'price' => 2000.00,
            'discount' => 200.00,
            'final_price' => 1800.00,
            'quantity' => 1,
        ]);

        $this->assertTrue(Sale::existsByExternalId('test_003'));
        $this->assertFalse(Sale::existsByExternalId('test_999'));
    }

    /**
     * Тест кастинга типов
     */
    public function test_casts_types_correctly(): void
    {
        $sale = Sale::create([
            'external_id' => 'test_004',
            'sale_date' => '2024-01-15',
            'article' => 'ART126',
            'price' => 1000.50,
            'discount' => 100.25,
            'final_price' => 900.25,
            'quantity' => 3,
            'additional_data' => ['key' => 'value'],
        ]);

        $this->assertIsString($sale->price);
        $this->assertIsArray($sale->additional_data);
        $this->assertInstanceOf(\Carbon\Carbon::class, $sale->sale_date);
    }
}
