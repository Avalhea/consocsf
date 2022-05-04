<?php

namespace App\Controller;

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
        UserRepository $userRepository, LieuRepository $lieuRepository, StatutRepository $statutRepository
    ): Response {


        $user = $userRepository->find($this->getUser()->getId());
        if ($user->getEchelle()->getId() == 1) {
            if ($lieuRepository->findOneBy(['user'=>$user,'statut'=>$statutRepository->find(1)])){
                $lieu = $lieuRepository->findOneBy(['user'=>$user,'statut'=>$statutRepository->find(1)]);
                return $this->redirectToRoute('formulaire_presentation',array('idLieu'=>$lieu->getId()));
            }
        }



        if (count($user->getLieux()) >0 && $user->getEchelle()->getId() === 1) {
            $stop = 'Stop';
        }
        else {
            $stop = 'pasStop';
        }

        return $this->render('home/index.html.twig',compact('stop'));
    }

    #[Route('/miniaide', name: 'miniaide')]
    public function miniaide(
        UserRepository $repository
    ): Response {
        return $this->render('home/miniaide.html.twig');
    }

}
