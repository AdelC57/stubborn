<?php

namespace App\Service;

use App\Entity\Sweatshirt;
use App\Repository\SweatshirtRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(
        private RequestStack $requestStack,
        private SweatshirtRepository $sweatshirtRepository
    ) {
    }

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    /**
     * Retourne le panier brut : [ "id_taille" => quantite ]
     * ex: "3_M" => 2
     */
    public function getCart(): array
    {
        return $this->getSession()->get(self::SESSION_KEY, []);
    }

    public function add(int $sweatshirtId, string $size): void
    {
        $cart = $this->getCart();
        $key = $sweatshirtId . '_' . $size;

        if (isset($cart[$key])) {
            $cart[$key]++;
        } else {
            $cart[$key] = 1;
        }

        $this->getSession()->set(self::SESSION_KEY, $cart);
    }

    public function remove(int $sweatshirtId, string $size): void
    {
        $cart = $this->getCart();
        $key = $sweatshirtId . '_' . $size;

        unset($cart[$key]);

        $this->getSession()->set(self::SESSION_KEY, $cart);
    }

    /**
     * Retourne les items enrichis avec les données du Sweatshirt
     * pour affichage dans le template panier.
     */
    public function getCartItems(): array
    {
        $cart = $this->getCart();
        $items = [];

        foreach ($cart as $key => $quantity) {
            [$sweatshirtId, $size] = explode('_', $key);
            $sweatshirt = $this->sweatshirtRepository->find($sweatshirtId);

            if ($sweatshirt) {
                $items[] = [
                    'sweatshirt' => $sweatshirt,
                    'size' => $size,
                    'quantity' => $quantity,
                    'subtotal' => $sweatshirt->getPrice() * $quantity,
                ];
            }
        }

        return $items;
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getCartItems() as $item) {
            $total += $item['subtotal'];
        }

        return $total;
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::SESSION_KEY);
    }
}