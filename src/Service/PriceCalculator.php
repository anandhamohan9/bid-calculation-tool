<?php

namespace App\Service;

class PriceCalculator
{
    private const BUYER_FEES = [
        'common' => ['min' => 10, 'max' => 50, 'rate' => 0.10],
        'luxury' => ['min' => 25, 'max' => 200, 'rate' => 0.10],
    ];

    private const SELLER_FEES = [
        'common' => 0.02,
        'luxury' => 0.04,
    ];

    private const ASSOCIATION_FEES = [
        500 => 5,
        1000 => 10,
        3000 => 15,
        PHP_INT_MAX => 20,
    ];

    private const STORAGE_FEE = 100;


    public function calculateTotalPrice(float $price, string $vehicleType): array
    {
        $vehicleType = strtolower($vehicleType);

        if (!isset(self::BUYER_FEES[$vehicleType]) && !isset(self::SELLER_FEES[$vehicleType])) {
            throw new \UnexpectedValueException("Invalid vehicle type : $vehicleType");
        }

        //buyer fee 
        $buyerRate = self::BUYER_FEES[$vehicleType]['rate'];
        if ($vehicleType === 'common') {
            $buyerFee = max(10, min(50, $price * $buyerRate));
        } elseif ($vehicleType === 'luxury') {
            $buyerFee = max(25, min(200, $price * $buyerRate));
        }

        //seller fee
        $sellerFee = $price * self::SELLER_FEES[$vehicleType];

        //Association Fee
        $associationFee = 20;
        foreach (self::ASSOCIATION_FEES as $limit => $fee) {
            if ($price <= $limit) {
                $associationFee = $fee;
                break;
            }
        }
        $totalPrice = $price + $buyerFee + $sellerFee + $associationFee + self::STORAGE_FEE;

        if(!isset($totalPrice)){
            throw new \UnexpectedValueException("Error on total price calculation");
        }
    
        $resultArray = array(
            'totalPrice' => $totalPrice,
            'breakDown' => compact('buyerFee', 'sellerFee', 'associationFee') + ['storageFee' => self::STORAGE_FEE]
        );
        return $resultArray;
    }
}
