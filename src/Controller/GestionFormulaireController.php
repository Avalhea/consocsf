<?php

namespace App\Controller;

use App\Entity\ActionJustice;
use App\Entity\Communication;
use App\Entity\Lieu;
use App\Repository\EchelleRepository;
use App\Repository\LieuRepository;
use App\Repository\UDRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/gestion/formulaire', name: 'gestion_formulaire')]
class GestionFormulaireController extends AbstractController
{
    #[Route('/ud', name: '_ud')]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository, LieuRepository $lieuRepository, EchelleRepository $echelleRepository, UDRepository $UDRepository): Response
    {
        if (count($lieuRepository->findBy(['echelle' => '2'])) < count($UDRepository->findAll())) {

        for ($i = 1; $i < count($UDRepository->findAll()) + 1; $i++) {
            if (count($lieuRepository->findBy(['echelle' => '2', 'UD' => $i])) == 0) {
                $ud = new Lieu();
                $ud->setEchelle($echelleRepository->find(2));
                $ud->setUD($UDRepository->find($i));
                $ud->setNom($UDRepository->find($i)->getLibelle());
                $entityManager->persist($ud);
                $entityManager->flush();
            }
        }
    }
            $UDs = $lieuRepository->findBy(['echelle' => '2']);


            foreach ($UDs as $UD) {
                $sections = $UD->getUD()->getLieux();
                $nbAcc=0;
                $actionConjointes=0;
                $nbAteliers=0;
//
                foreach($sections as $section) {

                    //          Action en Justice
                    $nbAcc = $nbAcc + $section->getActionJustice()->getNbAccompagnement() ;
                    $actionConjointes = $actionConjointes + $section->getActionJustice()->getNbActionConjointe();
                    //          Ateliers
                    $nbAteliers = $nbAteliers + count($section->getAtelier());

                    //          Communication  oui
//                      Lieu Nb Sujets TypesCom
                    for($i=0; $i < count($section->getCommunication())+1;$i++) {

                    }
                    $Communications = $section->getCommunication();
                    //          Dossier

                    //          Formations

                    //          Evenements

                    //          Permanence

                    //          Representation

                }

                $actionJustice = new ActionJustice();
                $actionJustice->setNbAccompagnement($nbAcc);
                $actionJustice->setNbActionConjointe($actionConjointes );
                $UD->setActionJustice($actionJustice);
//          Ateliers

                $UD->setNbAteliers($nbAteliers);


//          Communication

//          Dossier

//          Formations

//          Evenements

//          Permanence

//          Representation
            }




        return $this->render('gestion_formulaire/gestionform.html.twig', [
            'controller_name' => 'GestionFormulaireController',
        ]);
    }
}
