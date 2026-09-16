<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeService
{
    public function __construct(
        private string $stripeSecretKey
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    /**
     * Construit les line items Stripe à partir du panier.
     * Extrait en méthode publique pour être testable sans appel réseau.
     *
     * @param array $cartItems Liste d'items ['sweatshirt' => Sweatshirt, 'size' => string, 'quantity' => int]
     */
    public function buildLineItems(array $cartItems): array
    {
        $lineItems = [];

        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['sweatshirt']->getName() . ' (Taille ' . $item['size'] . ')',
                    ],
                    'unit_amount' => (int) round($item['sweatshirt']->getPrice() * 100),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        return $lineItems;
    }

    /**
     * Crée une session de paiement Stripe Checkout
     * à partir des articles du panier.
     */
    public function createCheckoutSession(array $cartItems, string $successUrl, string $cancelUrl): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $this->buildLineItems($cartItems),
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }
}