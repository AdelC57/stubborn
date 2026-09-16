<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use App\Entity\Stock;
use App\Repository\SweatshirtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private const SIZES = ['XS', 'S', 'M', 'L', 'XL'];

    #[Route('/admin', name: 'app_admin')]
    public function index(SweatshirtRepository $sweatshirtRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'sweatshirts' => $sweatshirtRepository->findAll(),
            'sizes' => self::SIZES,
        ]);
    }

    #[Route('/admin/add', name: 'app_admin_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $sweatshirt = new Sweatshirt();
        $sweatshirt->setName($request->request->get('name'));
        $sweatshirt->setPrice((float) $request->request->get('price'));
        $sweatshirt->setImage($request->request->get('image'));
        $sweatshirt->setFeatured((bool) $request->request->get('featured'));

        foreach (self::SIZES as $size) {
            $stock = new Stock();
            $stock->setSize($size);
            $stock->setQuantity((int) $request->request->get('stock_' . $size, 0));
            $stock->setSweatshirt($sweatshirt);
            $em->persist($stock);
        }

        $em->persist($sweatshirt);
        $em->flush();

        $this->addFlash('success', 'Sweat-shirt ajouté.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/update/{id}', name: 'app_admin_update', methods: ['POST'])]
    public function update(Sweatshirt $sweatshirt, Request $request, EntityManagerInterface $em): Response
    {
        $sweatshirt->setName($request->request->get('name'));
        $sweatshirt->setPrice((float) $request->request->get('price'));
        $sweatshirt->setImage($request->request->get('image'));
        $sweatshirt->setFeatured((bool) $request->request->get('featured'));

        foreach ($sweatshirt->getStocks() as $stock) {
            $newQuantity = $request->request->get('stock_' . $stock->getSize());
            if ($newQuantity !== null) {
                $stock->setQuantity((int) $newQuantity);
            }
        }

        $em->flush();

        $this->addFlash('success', 'Sweat-shirt modifié.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/delete/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Sweatshirt $sweatshirt, EntityManagerInterface $em): Response
    {
        $em->remove($sweatshirt);
        $em->flush();

        $this->addFlash('success', 'Sweat-shirt supprimé.');

        return $this->redirectToRoute('app_admin');
    }
}