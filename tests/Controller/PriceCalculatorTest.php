<?php

use App\Service\PriceCalculator;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    private PriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    public function testCalculateTotalPriceCommon(): void
    {
        $price = 398;
        $vehicleType = 'common';

        $result = $this->calculator->calculateTotalPrice($price, $vehicleType);

        $expectedBuyerFee = max(10, min(50, $price * 0.10)); 
        $expectedSellerFee = $price * 0.02; 
        $expectedAssociationFee = 5; 
        $expectedStorageFee = 100;
        $expectedTotal = $price + $expectedBuyerFee + $expectedSellerFee + $expectedAssociationFee + $expectedStorageFee;

        $this->assertEqualsWithDelta($expectedTotal, $result['totalPrice'], 0.01);
        $this->assertEqualsWithDelta($expectedBuyerFee, $result['breakDown']['buyerFee'], 0.01);
        $this->assertEqualsWithDelta($expectedSellerFee, $result['breakDown']['sellerFee'], 0.01);
        $this->assertEquals($expectedAssociationFee, $result['breakDown']['associationFee']);
        $this->assertEquals($expectedStorageFee, $result['breakDown']['storageFee']);
    }

    public function testCalculateTotalPriceLuxury(): void
    {
        $price = 1800;
        $vehicleType = 'luxury';

        $result = $this->calculator->calculateTotalPrice($price, $vehicleType);

        $expectedBuyerFee = max(25, min(200, $price * 0.10)); 
        $expectedSellerFee = $price * 0.04; 
        $expectedAssociationFee = 15; 
        $expectedStorageFee = 100;
        $expectedTotal = $price + $expectedBuyerFee + $expectedSellerFee + $expectedAssociationFee + $expectedStorageFee;

        $this->assertEqualsWithDelta($expectedTotal, $result['totalPrice'], 0.01);
        $this->assertEqualsWithDelta($expectedBuyerFee, $result['breakDown']['buyerFee'], 0.01);
        $this->assertEqualsWithDelta($expectedSellerFee, $result['breakDown']['sellerFee'], 0.01);
        $this->assertEquals($expectedAssociationFee, $result['breakDown']['associationFee']);
        $this->assertEquals($expectedStorageFee, $result['breakDown']['storageFee']);
    }

    public function testCalculateTotalPriceWithInvalidType(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->calculator->calculateTotalPrice(500, 'invalidType');
    }
}
