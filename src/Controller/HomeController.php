<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {
        return $this->render('home/faq.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/home', name: 'home')]
    public function home(
        UserRepository $repository
    ): Response {
        return $this->render('home/index.html.twig');
    }

    #[Route('/miniaide', name: 'miniaide')]
    public function miniaide(
        UserRepository $repository
    ): Response {
        return $this->render('home/miniaide.html.twig');
    }

}
