<?php

namespace App\Controller;

use App\Repository\EchelleRepository;
use App\Repository\LieuRepository;
use App\Repository\StatutRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @IsGranted("ROLE_SECTION")
     */
    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {
        return $this->render('home/faq.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @IsGranted("ROLE_SECTION")
     */
    #[Route('/home', name: 'home')]
    public function home(
        UserRepository $userRepository, LieuRepository $lieuRepository, EchelleRepository $echelleRepository, StatutRepository $statutRepository
    ): Response {

        // On recupere l'utilisation authentifié
        $user = $userRepository->find($this->getUser()->getId());
        // condition lié à l'affichage du contenu en fonction du statut dossier (validé ou en cours)
        if ($user->getEchelle()->getId() == 1) {
            if ($lieuRepository->findOneBy(['user'=>$user,'statut'=>$statutRepository->find(1)])){
                $lieu = $lieuRepository->findOneBy(['user'=>$user,'statut'=>$statutRepository->find(1)]);
                return $this->redirectToRoute('formulaire_presentation',array('idLieu'=>$lieu->getId()));
            }
        }

        if ($user->getEchelle()->getId() > 1) {
            $lieuxUser = $lieuRepository->findBy(['user'=>$user,'statut'=>$statutRepository->find(1)]);
        }
        else {
            $lieuxUser = '';
        }


        // Condition lié à l'affichage des options de la page en fonction de l'echelle (1= affichage section)
        if (count($user->getLieux()) >0 && $user->getEchelle()->getId() === 1 ) {
            $stop = 'Stop';
        }
        else {
            $stop = 'pasStop';
        }
        // Si connecté en UD alors possible affichage des dossiers de ses sections en cours
        if (count($lieuRepository->findBy(['user'=>$user,'echelle'=>$echelleRepository->find(2)]) ) > 0 && $user->getEchelle()->getId() === 2){

            $stop = 'Stop';
        }
        return $this->render('home/index.html.twig',compact('stop','lieuxUser'));
    }

    #[Route('/miniaide', name: 'miniaide')]
    public function miniaide(
        UserRepository $repository
    ): Response {
        return $this->render('home/miniaide.html.twig');
    }

}
