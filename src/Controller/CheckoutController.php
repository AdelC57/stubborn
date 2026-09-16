<?php

namespace App\Controller;

use App\Service\CartService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(CartService $cartService, StripeService $stripeService): RedirectResponse
    {
        $cartItems = $cartService->getCartItems();

        if (empty($cartItems)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        $successUrl = $this->generateUrl('app_checkout_success', [], 0) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $this->generateUrl('app_cart', [], 0);

        $session = $stripeService->createCheckoutSession($cartItems, $successUrl, $cancelUrl);

        return new RedirectResponse($session->url, 303);
    }

    #[Route('/checkout/success', name: 'app_checkout_success')]
    public function success(CartService $cartService): Response
    {
        $cartService->clear();

        return $this->render('checkout/success.html.twig');
    }
}