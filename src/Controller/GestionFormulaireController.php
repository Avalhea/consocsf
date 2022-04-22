<?php

namespace App\Controller;

use App\Entity\ActionJustice;
use App\Entity\Communication;
use App\Entity\Dossier;
use App\Entity\Evenement;
use App\Entity\Formations;
use App\Entity\Lieu;
use App\Entity\Permanence;
use App\Entity\Representation;
use App\Repository\CategorieRepRepository;
use App\Repository\CommunicationRepository;
use App\Repository\DossierRepository;
use App\Repository\EchelleRepository;
use App\Repository\LieuRepository;
use App\Repository\RepresentationRepository;
use App\Repository\StatutRepository;
use App\Repository\TypeCommunicationRepository;
use App\Repository\TypologieDossierRepository;
use App\Repository\UDRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/gestion/formulaire', name: 'ud')]
class GestionFormulaireController extends AbstractController
{
    #[Route('/ud/{idUD}', name: '_ud', requirements: ['idUD' => '\d+'])]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository,StatutRepository $statutRepository,RepresentationRepository $representationRepository , CategorieRepRepository $categorieRepRepository ,TypologieDossierRepository $typologieDossierRepository, DossierRepository $dossierRepository , CommunicationRepository $communicationRepository , TypeCommunicationRepository $typeCommunicationRepository, LieuRepository $lieuRepository, EchelleRepository $echelleRepository, UDRepository $UDRepository, $idUD = 0): Response
    {
        if (count($lieuRepository->findBy(['echelle' => '2'])) < count($UDRepository->findAll())) {
            for ($i = 1; $i < count($UDRepository->findAll()) + 1; $i++) {
                $ud = new Lieu();
                $ud->setEchelle($echelleRepository->find(2));
                $ud->setUD($UDRepository->find($i));
                $ud->setNom($UDRepository->find($i)->getLibelle());
                $entityManager->persist($ud);
                $entityManager->flush();
            }
        }

            $UDs = $lieuRepository->findBy(['echelle' => $echelleRepository->find(2)]);



            foreach ($UDs as $UD) {
                $sections = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(1),'UD'=>$UD->getUD(),'statut'=>$statutRepository->find(2)]);



                $nbAcc=0;
                $actionConjointes=0;
                $nbAteliers=0;
                $nbParti=0;
                $nbFormationsAnnee=0;
                $themesFormation=0;
                $events=0;
                $nbHeures=0;
                $nbJours=0;
                $nbDossierSimple=0;
                $nbDossierDiff=0;
                $nbSalaries=0;
                $nbBenevoles=0;
                $nbConsoTel=0;


//
                foreach($sections as $section) {

                    $nbSalaries = $nbSalaries+$section->getNbSalaries();
                    $nbBenevoles = $nbBenevoles+$section->getNbBenevole();
                    $nbConsoTel = $nbConsoTel+$section->getNbConsomRensTel();


                    //          Action en Justice
                    $nbAcc = $nbAcc + $section->getActionJustice()->getNbAccompagnement() ;
                    $actionConjointes = $actionConjointes + $section->getActionJustice()->getNbActionConjointe();

                    //          Ateliers
                    $nbAteliers = $nbAteliers + count($section->getAtelier());
                    $ateliers=$section->getAtelier();

                    foreach($ateliers as $atelier) {
                        $nbParti=$nbParti+$atelier->getNbPersonnesTotal();
                    }

                    //          Communication
                    for ($i=1; $i < count($typeCommunicationRepository->findAll())+1; $i++) {
                        $nb=0;

                        foreach($sections as $sec) {
                            $com = $communicationRepository->findOneBy( ['lieu'=>$sec, 'typeCommunication'=>$typeCommunicationRepository->find($i)]);
                            $nb=$nb+$com->getNombre();
                        }

                        if(!$communicationRepository->findOneBy(['lieu'=>$UD,'typeCommunication'=>$typeCommunicationRepository->find($i)])) {
                            $communication = new Communication();
                        }
                        else {
                            $communication = $communicationRepository->findOneBy(['lieu'=>$UD,'typeCommunication'=>$typeCommunicationRepository->find($i)]);
                        }

                        $communication->setTypeCommunication($typeCommunicationRepository->find($i));
                        $communication->setNombre( $nb);
                        $communication->setLieu($UD);
                        $entityManager->persist($communication);
                        $entityManager->flush();

                    }

                    //          Dossier
                    for ($i=1; $i < count($typologieDossierRepository->findAll())+1; $i++) {
                        $nb=0;

                        foreach($sections as $sec) {
                            $dos = $dossierRepository->findOneBy( ['lieu'=>$sec, 'typologieDossier'=>$typologieDossierRepository->find($i)]);
                            $nb=$nb+$dos->getNbDossiers();
                        }

                        if(!$dossierRepository->findOneBy(['lieu'=>$UD,'typologieDossier'=>$typologieDossierRepository->find($i)])) {
                            $dossier = new Dossier();
                        }
                        else {
                            $dossier = $dossierRepository->findOneBy(['lieu'=>$UD,'typologieDossier'=>$typologieDossierRepository->find($i)]);
                        }

                        $dossier->setTypologieDossier($typologieDossierRepository->find($i));
                        $dossier->setNbDossiers($nb);
                        $dossier->setLieu($UD);

                        $entityManager->persist($dossier);
                        $entityManager->flush();
                    }

                    //          Formations
                    $nbFormationsAnnee=$nbFormationsAnnee+$section->getFormations()->getNbFormationsAnnee();
                    $themesFormation=$themesFormation . ', ' .$section->getFormations()->getThemeFormationEtParticipants();


                    //          Evenements
                    $events=$events . ', ' . $section->getEvenement()->getDetailEvenement();

                    //          Permanence
                    $nbHeures=$nbHeures+$section->getPermanence()->getNbHeures();
                    $nbJours=$nbJours+$section->getPermanence()->getNbJours();
                    $nbDossierSimple=$nbDossierSimple+$section->getPermanence()->getNbDossierSimple();
                    $nbDossierDiff=$nbDossierDiff+$section->getPermanence()->getNbDossierDifficile();



                    //          Representation

                    for ($i=1; $i < count($categorieRepRepository->findAll())+1; $i++) {
                        $nb=0;
                        foreach($sections as $sec) {
                            $repi = $representationRepository->findOneBy( ['lieu'=>$sec, 'categorie'=>$categorieRepRepository->find($i)]);
                            $nb=$nb+$repi->getFrequence();
                        }

                        if(!$representationRepository->findOneBy(['lieu'=>$UD,'categorie'=>$categorieRepRepository->find($i)])) {
                            $representation = new Representation();
                        }
                        else {
                            $representation = $representationRepository->findOneBy(['lieu'=>$UD,'categorie'=>$categorieRepRepository->find($i)]);
                        }
                        $representation->setCategorie($categorieRepRepository->find($i));
                        $representation->setFrequence( $nb);
                        $representation->setLieu($UD);
                        $entityManager->persist($representation);
                        $entityManager->flush();
                    }

                }


//          ActionJustice
                $actionJustice = new ActionJustice();
                $actionJustice->setNbAccompagnement($nbAcc);
                $actionJustice->setNbActionConjointe($actionConjointes );
                $UD->setActionJustice($actionJustice);

//          Ateliers
                $UD->setNbAteliers($nbAteliers)->setNbPartiAteliers($nbParti);

//          Communication  ok
//          Dossier  ok

//          Formations
                if(!$UD->getFormations()){
                $formation = new Formations();
                }
                else {
                    $formation = $UD->getFormations();
                }
                $formation->setNbFormationsAnnee($nbFormationsAnnee)->setThemeFormationEtParticipants($themesFormation);

//          Evenements

                if(!$UD->getFormations()){
                    $evenement = new Evenement();
                }
                else {
                    $evenement = $UD->getEvenement();
                }
                $evenement->setDetailEvenement($events);


//          Permanence

                if(!$UD->getFormations()){
                    $permanence = new Permanence();
                }
                else {
                    $permanence = $UD->getPermanence();
                }
                $permanence->setNbHeures($nbHeures)->setNbJours($nbJours)->setNbDossierSimple($nbDossierSimple)->setNbDossierDifficile($nbDossierDiff);

//          Representation ok

//  set
                $UD->setFormations($formation)->setEvenement($evenement)->setPermanence($permanence)->setNbSalaries($nbSalaries)->setNbBenevole($nbBenevoles)->setNbConsomRensTel($nbConsoTel);

            $entityManager->persist($UD);
            $entityManager->flush();

            }
                $user = $userRepository->find($this->getUser()->getId());

            if($idUD == 0) {
                $UD = $lieuRepository->findOneBy(['echelle'=>$echelleRepository->find(2),'UD'=>$user->getUd()]);
            }

            else {
                $UD = $lieuRepository->find($idUD);
                if ($user->getEchelle() !== $echelleRepository->find(3)) {
                    if ($user->getUd() !== $UD->getUD()) {
                        return $this->redirectToRoute('home');
                    }
                }
            }

        return $this->render('ud/ud.html.twig',
            compact( 'UD'));
    }
}
