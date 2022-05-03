<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculsController extends AbstractController
{
    /**
     * @IsGranted("ROLE_UD")
     */
    #[Route('/calculs', name: 'app_calculs')]
    public function index(): Response
    {
        return $this->render('calculs/gestionform.html.twig', [
            'controller_name' => 'CalculsController',
        ]);
    }

}
