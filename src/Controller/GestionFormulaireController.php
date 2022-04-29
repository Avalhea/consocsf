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
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestion/formulaire', name: 'gestion_formulaire')]
class GestionFormulaireController extends AbstractController
{
    #[Route('/recap/{idLieu}', name: '_recap', requirements: ['idLieu' => '\d+'])]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository, StatutRepository $statutRepository, RepresentationRepository $representationRepository, CategorieRepRepository $categorieRepRepository, TypologieDossierRepository $typologieDossierRepository, DossierRepository $dossierRepository, CommunicationRepository $communicationRepository, TypeCommunicationRepository $typeCommunicationRepository, LieuRepository $lieuRepository, EchelleRepository $echelleRepository, UDRepository $UDRepository, $idLieu = 0): Response
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

        if (count($lieuRepository->findBy(['echelle' => '3'])) == 0) {
                $national = new Lieu();
                $national->setEchelle($echelleRepository->find(3));
                $national->setNom('National');
                $entityManager->persist($national);
                $entityManager->flush();
            }



        for($j=0;$j<2;$j++) {

            if($j==0) {
                $lieux = $lieuRepository->findBy(['echelle' => $echelleRepository->find(2)]);
            }
            else if($j==1) {
                $lieux = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(3)]);
            }

            foreach ($lieux as $lieu) {

                if($j==0) {
                    $sousLieux = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'UD' => $lieu->getUD(), 'statut' => $statutRepository->find(2)]);
                }
                else if($j==1) {
                    $sousLieux = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(2)]);
                }


                $nbAcc = 0;
                $actionConjointes = 0;
                $nbAteliers = 0;
                $nbParti = 0;
                $nbFormationsAnnee = 0;
                $themesFormation = ' ';
                $events = 0;
                $nbHeures = 0;
                $nbJours = 0;
                $nbDossierSimple = 0;
                $nbDossierDiff = 0;
                $nbSalaries = 0;
                $nbBenevoles = 0;
                $nbConsoTel = 0;


//
                foreach ($sousLieux as $sousLieu) {

                    $nbSalaries = $nbSalaries + $sousLieu->getNbSalaries();
                    $nbBenevoles = $nbBenevoles + $sousLieu->getNbBenevole();
                    $nbConsoTel = $nbConsoTel + $sousLieu->getNbConsomRensTel();


                    //          Action en Justice
                    $nbAcc = $nbAcc + $sousLieu->getActionJustice()->getNbAccompagnement();
                    $actionConjointes = $actionConjointes + $sousLieu->getActionJustice()->getNbActionConjointe();

                    //          Ateliers
                    $nbAteliers = $nbAteliers + count($sousLieu->getAtelier());
                    $ateliers = $sousLieu->getAtelier();

                    foreach ($ateliers as $atelier) {
                        $nbParti = $nbParti + $atelier->getNbPersonnesTotal();
                    }

                    //          Communication
                    for ($i = 1; $i < count($typeCommunicationRepository->findAll()) + 1; $i++) {
                        $nb = 0;

                        if (!$communicationRepository->findOneBy(['lieu' => $lieu, 'typeCommunication' => $typeCommunicationRepository->find($i)])) {
                            $communication = new Communication();
                        } else {
                            $communication = $communicationRepository->findOneBy(['lieu' => $lieu, 'typeCommunication' => $typeCommunicationRepository->find($i)]);
                        }

                        foreach ($sousLieux as $sousL) {
                            $com = $communicationRepository->findOneBy(['lieu' => $sousL, 'typeCommunication' => $typeCommunicationRepository->find($i)]);
                            if ($com) {
                                $nb = $nb + $com->getNombre();
                            }
                        }


                        $communication->setTypeCommunication($typeCommunicationRepository->find($i));
                        $communication->setNombre($nb);
                        $communication->setLieu($lieu);
                        $entityManager->persist($communication);
                        $entityManager->flush();

                    }

                    //          Dossier
                    for ($i = 1; $i < count($typologieDossierRepository->findAll()) + 1; $i++) {
                        $nb = 0;

                        if (!$dossierRepository->findOneBy(['lieu' => $lieu, 'typologieDossier' => $typologieDossierRepository->find($i)])) {
                            $dossier = new Dossier();
                        } else {
                            $dossier = $dossierRepository->findOneBy(['lieu' => $lieu, 'typologieDossier' => $typologieDossierRepository->find($i)]);
                        }

                        foreach ($sousLieux as $sousL) {
                            $dos = $dossierRepository->findOneBy(['lieu' => $sousL, 'typologieDossier' => $typologieDossierRepository->find($i)]);
                            if($dos){
                            $nb = $nb + $dos->getNbDossiers();
                            }
                        }


                        $dossier->setTypologieDossier($typologieDossierRepository->find($i));
                        $dossier->setNbDossiers($nb);
                        $dossier->setLieu($lieu);

                        $entityManager->persist($dossier);
                        $entityManager->flush();
                    }

                    //          Formations
                    $nbFormationsAnnee = $nbFormationsAnnee + $sousLieu->getFormations()->getNbFormationsAnnee();
                    $themesFormation = $themesFormation . ', ' . $sousLieu->getFormations()->getThemeFormationEtParticipants();


                    //          Evenements
                    $events = $events . ', ' . $sousLieu->getEvenement()->getDetailEvenement();

                    //          Permanence
                    $nbHeures = $nbHeures + $sousLieu->getPermanence()->getNbHeures();
                    $nbJours = $nbJours + $sousLieu->getPermanence()->getNbJours();
                    $nbDossierSimple = $nbDossierSimple + $sousLieu->getPermanence()->getNbDossierSimple();
                    $nbDossierDiff = $nbDossierDiff + $sousLieu->getPermanence()->getNbDossierDifficile();


                    //          Representation

                    for ($i = 1; $i < count($categorieRepRepository->findAll()) + 1; $i++) {
                        $nb = 0;

                        if (!$representationRepository->findOneBy(['lieu' => $lieu, 'categorie' => $categorieRepRepository->find($i)])) {
                            $representation = new Representation();
                        } else {
                            $representation = $representationRepository->findOneBy(['lieu' => $lieu, 'categorie' => $categorieRepRepository->find($i)]);
                        }

                        foreach ($sousLieux as $sousL) {
                            $repi = $representationRepository->findOneBy(['lieu' => $sousL, 'categorie' => $categorieRepRepository->find($i)]);
                            if($repi) {
                                $nb = $nb + $repi->getFrequence();
                            }
                        }

                        $representation->setCategorie($categorieRepRepository->find($i));
                        $representation->setFrequence($nb);
                        $representation->setLieu($lieu);
                        $entityManager->persist($representation);
                        $entityManager->flush();
                    }

                }

//          ActionJustice
                if (!$lieu->getActionJustice()) {
                    $actionJustice = new ActionJustice();
                } else {
                    $actionJustice = $lieu->getActionJustice();
                }

                $actionJustice->setNbAccompagnement($nbAcc);
                $actionJustice->setNbActionConjointe($actionConjointes);
                $lieu->setActionJustice($actionJustice);

//          Ateliers
                $lieu->setNbAteliers($nbAteliers)->setNbPartiAteliers($nbParti);

//          Communication  ok
//          Dossier  ok

//          Formations
                if (!$lieu->getFormations()) {
                    $formation = new Formations();
                } else {
                    $formation = $lieu->getFormations();
                }
                $formation->setNbFormationsAnnee($nbFormationsAnnee)->setThemeFormationEtParticipants($themesFormation);

//          Evenements

                if (!$lieu->getFormations()) {
                    $evenement = new Evenement();
                } else {
                    $evenement = $lieu->getEvenement();
                }
                $evenement->setDetailEvenement($events);


//          Permanence

                if (!$lieu->getFormations()) {
                    $permanence = new Permanence();
                } else {
                    $permanence = $lieu->getPermanence();
                }
                $permanence->setNbHeures($nbHeures)->setNbJours($nbJours)->setNbDossierSimple($nbDossierSimple)->setNbDossierDifficile($nbDossierDiff);

//          Representation ok

//  set
                $lieu->setFormations($formation)->setEvenement($evenement)->setPermanence($permanence)->setNbSalaries($nbSalaries)->setNbBenevole($nbBenevoles)->setNbConsomRensTel($nbConsoTel);

                $entityManager->persist($lieu);
                $entityManager->flush();
            }
        }
            $user = $userRepository->find($this->getUser()->getId());

            if ($idLieu == 0) {
                $lieu = $lieuRepository->findOneBy(['echelle' => $echelleRepository->find(2), 'UD' => $user->getUd()]);
            } else {
                $lieu = $lieuRepository->find($idLieu);
                if ($user->getEchelle() !== $echelleRepository->find(3)) {
                    if ($user->getUd() !== $lieu->getUD()) {
                        return $this->redirectToRoute('home');
                    }
                }
            }

            if($lieu->getEchelle()->getId() < 3  ) {
                $Sections = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'UD' => $lieu->getUD(), 'statut' => $statutRepository->find(2)]);
            }
            else {
                $Sections = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'statut' => $statutRepository->find(2)]);
            }

            return $this->render('recap/recap.html.twig',
                compact('lieu', 'Sections'));
    }

    #[Route('/pdf/{id}', name: 'detailBilanPdf', requirements: ['idUD' => '\d+'])]
    public function generatePdfRecap(PdfService $pdf,LieuRepository $lieuRepository,StatutRepository $statutRepository, EchelleRepository $echelleRepository,$id = 0)
    {
        $lieu = $lieuRepository->find($id);
        if($lieu->getEchelle()->getId() < 3  ) {
            $Sections = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'UD' => $lieu->getUD(), 'statut' => $statutRepository->find(2)]);
        }
        else {
            $Sections = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'statut' => $statutRepository->find(2)]);

        }
        $html = $this->render('recap/recap.html.twig', compact('lieu','Sections'));


        $name = 'Recapitulatif_dossier_conso_' . $lieu->getEchelle()->getLibelle() . '_' . $lieu->getNom() .'.PDF';

        $pdf->showPdfFile($html, $name);


    }
}
