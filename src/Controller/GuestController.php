<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GuestController extends AbstractController
{
    #[Route('/home', name: 'app_guest')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(Category::class)->findAll();
        return $this->render('guest/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
