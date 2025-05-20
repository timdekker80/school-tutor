<?php

// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list')]
    public function list(ProductRepository $productRepo, CategoryRepository $categoryRepo): Response
    {
        $categories = $categoryRepo->findAll();
        $products = $productRepo->findAll();

        return $this->render('product/list.html.twig', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}

