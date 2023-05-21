<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentRepository;
use App\Repository\SaleRepository;
use App\Repository\ClientRepository;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Entity\Sale;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(CommentRepository $commentRepository,
    SaleRepository $saleRepository,ProduitRepository $productRepository,
    ClientRepository $clientRepository): Response
    {
        $recentSales = $saleRepository->findBy([], ['id' => 'DESC'], 5);
        $topSales = $saleRepository->findBy([], ['quantity' => 'DESC'], 6);
        $sales = $saleRepository->findAll();
        $revenue=0;
        foreach ($sales as $sale) {
            $revenue+=$sale->getTotalPrice();
        }
        $products = $productRepository->findAll();
        $data=[];
        $name=[];
        foreach ($products as $product) {
            $ventes=$saleRepository->findBy(['product'=>$product]);
            $data[] = (count($ventes));
            $name[] = $product->getNom();
        }
    
        $comments = $commentRepository->findBy([], ['id' => 'DESC'], 20);
        $customers = $clientRepository->findAll();
        return $this->render('admin/index.html.twig',[
            'sales'=>$sales,
            'recentSales'=>$recentSales,
            'revenue'=>$revenue,
            'comments'=>$comments,
            'customers'=>$customers,
            'topSales'=>$topSales,
            'products'=>$products,
            'data'=>$data,
            'name'=>$name,
        ]);
    }
    #[Route('/orders', name: 'app_orders')]
    public function orders(CommandeRepository $commandeRepository,): Response
    {
        $commandes = $commandeRepository->findAll();
        return $this->render('admin/orders.html.twig',[
            'commandes'=>$commandes
        ]);
    }

    #[Route('/sales', name: 'app_sales')]
    public function sales(SaleRepository $saleRepository,): Response
    {
        $sales = $saleRepository->findAll();
        return $this->render('admin/sales.html.twig',[
            'sales'=>$sales,
        ]);
    }

    #[Route('/customers', name: 'app_customers')]
    public function customers(ClientRepository $clientRepository,): Response
    {
        $customers = $clientRepository->findAll();
        return $this->render('admin/customers.html.twig',[
            'customers'=>$customers
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function products(ProduitRepository $productRepository,): Response
    {
        $products = $productRepository->findAll();
        return $this->render('admin/products.html.twig',[
            'products'=>$products
        ]);
    }

    #[Route('/categories', name: 'app_categories')]
    public function categories(CategorieRepository $categorieRepository,): Response
    {
        $categories = $categorieRepository->findAll();
        return $this->render('admin/categories.html.twig',[
            'categories'=>$categories
        ]);
    }

    #[Route('/comments', name: 'app_comments')]
    public function comments(CommentRepository $commentRepository,): Response
    {
        $comments = $commentRepository->findAll();
        return $this->render('admin/comments.html.twig',[
            'comments'=>$comments
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        $this->redirectToRoute('login');
    }

    #[Route('/confirm/{id}', name: 'app_confirm')]
    public function confirm($id,CommandeRepository $commandeRepository,
    SaleRepository $saleRepository,): Response
    {
        $commande=$commandeRepository->find($id);
        if($commande){
            $products=$commande->getProduits();
            foreach ($products as $product) {
                $sale=new Sale();
                $sale->setClient($commande->getClient());
                $sale->setDate($commande->getDateCommande());
                $sale->setShippingAddress($commande->getAdresse());
                $sale->setStatus('pending');
                $sale->setQuantity(1);
                $sale->setTotalPrice($commande->getMontantTotal());
                $sale->setProduct($product);
                $saleRepository->save($sale,true);
            }
            $commandeRepository->remove($commande,true);
        }
        return $this->redirectToRoute('app_sales');
    }

    
}
