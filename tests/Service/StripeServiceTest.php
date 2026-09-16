<?php

namespace App\Tests\Service;

use App\Entity\Sweatshirt;
use App\Service\StripeService;
use PHPUnit\Framework\TestCase;

class StripeServiceTest extends TestCase
{
    private StripeService $stripeService;

    protected function setUp(): void
    {
        // Clé factice : on ne fait aucun appel réseau dans ces tests
        $this->stripeService = new StripeService('sk_test_fake_key_for_unit_tests');
    }

    public function testBuildLineItemsWithSingleItem(): void
    {
        $sweatshirt = new Sweatshirt();
        $sweatshirt->setName('Blackbelt');
        $sweatshirt->setPrice(29.90);
        $sweatshirt->setImage('blackbelt.jpeg');
        $sweatshirt->setFeatured(true);

        $cartItems = [
            ['sweatshirt' => $sweatshirt, 'size' => 'M', 'quantity' => 1],
        ];

        $lineItems = $this->stripeService->buildLineItems($cartItems);

        $this->assertCount(1, $lineItems);
        $this->assertEquals('eur', $lineItems[0]['price_data']['currency']);
        $this->assertEquals('Blackbelt (Taille M)', $lineItems[0]['price_data']['product_data']['name']);
        $this->assertEquals(2990, $lineItems[0]['price_data']['unit_amount']); // 29.90€ -> 2990 centimes
        $this->assertEquals(1, $lineItems[0]['quantity']);
    }

    public function testBuildLineItemsWithMultipleItems(): void
    {
        $sweatshirt1 = new Sweatshirt();
        $sweatshirt1->setName('Pokeball');
        $sweatshirt1->setPrice(45.00);
        $sweatshirt1->setImage('pokeball.jpeg');
        $sweatshirt1->setFeatured(true);

        $sweatshirt2 = new Sweatshirt();
        $sweatshirt2->setName('Snow');
        $sweatshirt2->setPrice(32.00);
        $sweatshirt2->setImage('snow.jpeg');
        $sweatshirt2->setFeatured(false);

        $cartItems = [
            ['sweatshirt' => $sweatshirt1, 'size' => 'L', 'quantity' => 2],
            ['sweatshirt' => $sweatshirt2, 'size' => 'S', 'quantity' => 1],
        ];

        $lineItems = $this->stripeService->buildLineItems($cartItems);

        $this->assertCount(2, $lineItems);
        $this->assertEquals(4500, $lineItems[0]['price_data']['unit_amount']);
        $this->assertEquals(2, $lineItems[0]['quantity']);
        $this->assertEquals(3200, $lineItems[1]['price_data']['unit_amount']);
        $this->assertEquals(1, $lineItems[1]['quantity']);
    }

    public function testBuildLineItemsWithEmptyCart(): void
    {
        $lineItems = $this->stripeService->buildLineItems([]);

        $this->assertEmpty($lineItems);
    }
}