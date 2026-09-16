<?php

namespace App\Tests\Service;

use App\Entity\Sweatshirt;
use App\Repository\SweatshirtRepository;
use App\Service\CartService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CartServiceTest extends TestCase
{
    private CartService $cartService;
    private Session $session;
    private Sweatshirt $sweatshirt;

    protected function setUp(): void
    {
        // Session en mémoire, pas besoin d'un vrai navigateur
        $this->session = new Session(new MockArraySessionStorage());

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getSession')->willReturn($this->session);

        // Sweat-shirt factice pour les tests
        $this->sweatshirt = new Sweatshirt();
        $this->sweatshirt->setName('TestShirt');
        $this->sweatshirt->setPrice(30.0);
        $this->sweatshirt->setImage('test.jpg');
        $this->sweatshirt->setFeatured(false);

        // On simule le Repository pour qu'il retourne toujours notre sweat-shirt de test
        $repository = $this->createMock(SweatshirtRepository::class);
        $repository->method('find')->willReturn($this->sweatshirt);

        $this->cartService = new CartService($requestStack, $repository);
    }

    public function testCartIsEmptyByDefault(): void
    {
        $this->assertEmpty($this->cartService->getCart());
        $this->assertEquals(0.0, $this->cartService->getTotal());
    }

    public function testAddItemToCart(): void
    {
        $this->cartService->add(1, 'M');

        $cart = $this->cartService->getCart();
        $this->assertCount(1, $cart);
        $this->assertEquals(1, $cart['1_M']);
    }

    public function testAddSameItemTwiceIncreasesQuantity(): void
    {
        $this->cartService->add(1, 'M');
        $this->cartService->add(1, 'M');

        $cart = $this->cartService->getCart();
        $this->assertEquals(2, $cart['1_M']);
    }

    public function testAddDifferentSizesCreatesSeparateEntries(): void
    {
        $this->cartService->add(1, 'M');
        $this->cartService->add(1, 'L');

        $cart = $this->cartService->getCart();
        $this->assertCount(2, $cart);
    }

    public function testRemoveItemFromCart(): void
    {
        $this->cartService->add(1, 'M');
        $this->cartService->remove(1, 'M');

        $this->assertEmpty($this->cartService->getCart());
    }

    public function testGetTotalCalculatesCorrectly(): void
    {
        $this->cartService->add(1, 'M'); // 30€
        $this->cartService->add(1, 'M'); // +30€ (quantité 2)
        $this->cartService->add(1, 'L'); // +30€ (nouvelle taille)

        // 2 x 30€ (taille M) + 1 x 30€ (taille L) = 90€
        $this->assertEquals(90.0, $this->cartService->getTotal());
    }

    public function testClearEmptiesTheCart(): void
    {
        $this->cartService->add(1, 'M');
        $this->cartService->clear();

        $this->assertEmpty($this->cartService->getCart());
    }
}