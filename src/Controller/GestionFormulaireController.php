<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionFormulaireController extends AbstractController
{
    #[Route('/gestion/formulaire', name: 'app_gestion_formulaire')]
    public function index(): Response
    {
        return $this->render('gestion_formulaire/gestionform.html.twig', [
            'controller_name' => 'GestionFormulaireController',
        ]);
    }
}
