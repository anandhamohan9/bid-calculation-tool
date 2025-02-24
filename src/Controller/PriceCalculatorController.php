<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PriceCalculator;

class PriceCalculatorController extends AbstractController
{
    #[Route('/calculate-price', name: 'calculate_price', methods: ['GET'])]
    public function calculatePrice(Request $request, PriceCalculator $priceCalculator): JsonResponse
    {
        $basePrice = $request->query->get('basePrice');
        $vehicleType = $request->query->get('vehicleType');

        error_log("Base Price: $basePrice, Vehicle Type: $vehicleType");

        // Validate 
        if (!$basePrice || !$vehicleType) {
            return new JsonResponse(['error' => 'invalid parameters'], 400);
        }

        $basePrice = (float) $basePrice;

        // Call actual service
        $result = $priceCalculator->calculateTotalPrice($basePrice, $vehicleType);

        error_log("Calculated Price: " . json_encode($result));

        return $this->json([ $result]);
    }
}