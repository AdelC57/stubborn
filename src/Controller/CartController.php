<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getCartItems(),
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request, CartService $cartService): Response
    {
        $size = $request->request->get('size');
        $cartService->add($id, $size);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}/{size}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $id, string $size, CartService $cartService): Response
    {
        $cartService->remove($id, $size);

        return $this->redirectToRoute('app_cart');
    }
}