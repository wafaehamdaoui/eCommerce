<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SaleRepository;
use App\Entity\Sale;
use App\Form\SaleType;

#[Route('/sale')]
class SaleController extends AbstractController
{
    #[Route('/', name: 'app_sale')]
    public function index(): Response
    {
        return $this->render('sale/index.html.twig', [
            'controller_name' => 'SaleController',
        ]);
    }

    #[Route('/{id}', name: 'app_sale_edit')]
    public function edit($id,Sale $sale, SaleRepository $saleRepository, Request $request,): Response
    {
        $form = $this->createForm(SaleType::class, $sale);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $saleRepository->save($sale,true);
           return $this->redirectToRoute('app_sales');
       }

       return $this->render('sale/edit.html.twig', [
           'form' => $form->createView(),
           'sale' => $sale,
       ]);
    }

    #[Route('/delete/{id}', name: 'app_sale_delete')]
    public function delete(Request $request, $id, SaleRepository $saleRepository): Response
    {
        $sale=$saleRepository->find($id);
        if($sale){
            $saleRepository->remove($sale,true);
        }

        return $this->redirectToRoute('app_sales',);
    }

    #[Route('/new', name: 'app_sale_new')]
     public function AddLesson(Request $request, SaleRepository $saleRepository): Response
     {
         $sale = new Sale();
         $form = $this->createForm(SaleType::class, $sale);
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
             $saleRepository->save($sale);
             return $this->redirectToRoute('app_sales');
         }
         return $this->render('sale/new.html.twig', ['form' => $form->createView(),]);
     }
}
