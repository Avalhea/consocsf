<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculsController extends AbstractController
{
    #[Route('/calculs', name: 'app_calculs')]
    public function index(): Response
    {
        return $this->render('calculs/index.html.twig', [
            'controller_name' => 'CalculsController',
        ]);
    }
}
