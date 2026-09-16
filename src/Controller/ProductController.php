<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use App\Repository\SweatshirtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(Request $request, SweatshirtRepository $sweatshirtRepository): Response
    {
        $priceRange = $request->query->get('price_range');

        $sweatshirts = match ($priceRange) {
            '10-29' => $sweatshirtRepository->findByPriceRange(10, 29),
            '29-35' => $sweatshirtRepository->findByPriceRange(29, 35),
            '35-50' => $sweatshirtRepository->findByPriceRange(35, 50),
            default => $sweatshirtRepository->findAll(),
        };

        return $this->render('product/index.html.twig', [
            'sweatshirts' => $sweatshirts,
            'currentPriceRange' => $priceRange,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(Sweatshirt $sweatshirt): Response
    {
        return $this->render('product/show.html.twig', [
            'sweatshirt' => $sweatshirt,
        ]);
    }
}