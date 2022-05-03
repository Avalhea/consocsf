<?php

namespace App\Controller;

use App\Entity\ActionJustice;
use App\Entity\Atelier;
use App\Entity\Communication;
use App\Entity\Dossier;
use App\Entity\Dossiers;
use App\Entity\DossiersAutre;
use App\Entity\Evenement;
use App\Entity\Formation;
use App\Entity\Formations;
use App\Entity\Lieu;
use App\Entity\Permanence;
use App\Entity\Representation;
use App\Form\ActionsEnJusticeType;
use App\Form\AteliersConsoType;
use App\Form\CommunicationType;
use App\Form\DossierType;
use App\Form\FormationsType;
use App\Form\PermanencesType;
use App\Form\PermanenceType;
use App\Form\PresentationType;
use App\Form\RepresentationType;
use App\Form\VieAssociativeType;
use App\Repository\AtelierRepository;
use App\Repository\CategorieRepRepository;
use App\Repository\CommunicationRepository;
use App\Repository\DossierRepository;
use App\Repository\DossiersAutreRepository;
use App\Repository\EchelleRepository;
use App\Repository\LieuRepository;
use App\Repository\PermanenceRepository;
use App\Repository\RepresentationRepository;
use App\Repository\StatutRepository;
use App\Repository\TypeCommunicationRepository;
use App\Repository\TypologieDossierRepository;
use App\Repository\UDRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_SECTION")
 */
#[Route('/formulaire', name: 'formulaire')]
class FormulaireController extends AbstractController
{
    #[Route('/presentation/{idLieu}', name: '_presentation', requirements: ['idLieu' => '\d+'])]
    public function presentation(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, EchelleRepository $echelleRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                                 Request        $request, $idLieu = 0): Response
    {



        if ($idLieu == 0) {

            $lieu = new Lieu();
            $formPresentation = $this->createForm(PresentationType::class, $lieu);
            $formPresentation->handleRequest($request);

            if ($formPresentation->isSubmitted() && $formPresentation->isValid()) {

                $user = $userRepository->find($this->getUser()->getId());
                $lieu->setUser($user);
                $lieu->setStatut($statutRepository->find(1));
                $lieu->setEchelle($echelleRepository->find(1));

                $entityManager->persist($lieu);
                $entityManager->flush();

                return $this->redirectToRoute('formulaire_permanence', array('idLieu'=>$lieu->getId()));
            }

            return $this->renderForm('formulaire/presentation.html.twig',
                compact('formPresentation')
            );

        } else { //s'il existe déjà un lieu (ex : après avoir cliqué sur modifier au niveau des bilans US/Nationaux, ou quand on clique sur 'suivant')

            //TODO Faire en sorte que la page soit accessible qu'au user qui est lié au formulaire et aux Victoires / Elsa


            $lieu = $lieuRepository->find($idLieu);
            $formPresentation = $this->createForm(PresentationType::class, $lieu);
            $formPresentation->handleRequest($request);

            if ($formPresentation->isSubmitted() && $formPresentation->isValid()) {

                $entityManager->persist($lieu);
                $entityManager->flush();

                return $this->redirectToRoute('formulaire_permanence', array('idLieu'=>$idLieu));

            }
        }
        return $this->renderForm('formulaire/presentation.html.twig',
            compact('formPresentation')
        );
    }

    #[Route('/permanence/{idLieu}', name: '_permanence', requirements: ['idLieu' => '\d+'])]
    public function permanence(LieuRepository $lieuRepository, UDRepository $UDRepository, PermanenceRepository $permanenceRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                               Request $request, $idLieu): Response
    {

        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getPermanence() !== null) {
            $permanence = $lieu->getPermanence();
        }
        else {
            $permanence = new Permanence();

        }

        $formPermanence = $this->createForm(PermanenceType::class,$permanence);
        $formPermanence->handleRequest($request);

        if ($formPermanence->isSubmitted() && $formPermanence->isValid()) {
            $lieu->setPermanence($permanence);
            $entityManager->persist($permanence);

            $entityManager->flush();

            return $this->redirectToRoute('formulaire_typologieDossier', array('idLieu'=>$idLieu));
        }

        return $this->renderForm('formulaire/permanence.html.twig',
            compact('formPermanence',  'idLieu'));

    }

    #[Route('/typologieDossier/{idLieu}/{state}', name: '_typologieDossier', requirements: ['idLieu' => '\d+'])]
    public function typologieDossier(LieuRepository $lieuRepository, UDRepository $UDRepository, DossiersAutreRepository $dossiersAutreRepository , UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                                  Request $request, $idLieu, TypologieDossierRepository $typologieDossierRepository, $state=0): Response
    {

        $lieu = $lieuRepository->find($idLieu);
        $Dossier = $lieu->getDossier();


        if($state === 0 ) {


            if ($Dossier->count() === 0) {

                for ($i = 0, $iMax = count($typologieDossierRepository->findAll()); $i < $iMax; $i++) {

                    $dos = new Dossier();
                    $dos->setTypologieDossier($typologieDossierRepository->find($i + 1));
                    $lieu->addDossier($dos);
                    $entityManager->persist($dos);
                    $entityManager->flush();

                }
                    $dossiersAutres = new DossiersAutre();
                    $dossiersAutres->setLieu($lieu);
                    $entityManager->persist($dossiersAutres);
                    $entityManager->flush();

                $entityManager->persist($lieu);
                $entityManager->flush();
            }
        }

        if($state==='sub') {

            $dossiersAutres = $dossiersAutreRepository->findOneBy(['lieu'=>$lieu]) ;

            foreach($Dossier as $dos) {


                $nombre = $request->request->get('Nb'. $dos->getTypologieDossier()->getId());

                if($dos->getTypologieDossier() === $typologieDossierRepository->findOneBy(['libelle'=>'AUTRES'])) {
                    $dossiersAutres->setLibelle($request->request->get('AUTRES'));
                    $dossiersAutres->setNbDossiers($nombre);
                }

                $dos->setNbDossiers($nombre);
                $entityManager->persist($dos);
                $entityManager->flush();
            }
            return $this->redirectToRoute('formulaire_vieAssociative', array('idLieu'=>$idLieu));
        }

        $dossiersAutres = $dossiersAutreRepository->findOneBy(['lieu'=>$lieu]) ;

        return $this->render('formulaire/typologiedossier.twig',
            compact( 'Dossier', 'dossiersAutres', 'idLieu'));
    }




    #[Route('/vieAssociative/{idLieu}', name: '_vieAssociative', requirements: ['idLieu' => '\d+'])]
    public function vieAssociative(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                                   Request $request, $idLieu): Response
    {

        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getEvenement() !== null) {
            $Evenement = $lieu->getEvenement();
        }
        else {
            $Evenement = new Evenement();
        }

        $formVieAssociative = $this->createForm(VieAssociativeType::class,$lieu);
        $formVieAssociative->handleRequest($request);


        if ($formVieAssociative->isSubmitted() && $formVieAssociative->isValid()) {

            $entityManager->persist($lieu);

            $entityManager->flush();

            return $this->redirectToRoute('formulaire_formation', array('idLieu'=>$idLieu));
        }

        return $this->renderForm('formulaire/vieAssociative.twig',
            compact('formVieAssociative',  'idLieu'));

    }



    #[Route('/formation/{idLieu}', name: '_formation', requirements: ['idLieu' => '\d+'])]
    public function formation(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                              Request $request, $idLieu): Response
    {

        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getFormations() !== null) {
            $Formations = $lieu->getFormations();
        }
        else {
            $Formations = new Formations();
        }

        $formFormations = $this->createForm(FormationsType::class,$Formations);
        $formFormations->handleRequest($request);


        if ($formFormations->isSubmitted() && $formFormations->isValid()) {

            $lieu->setFormations($Formations);
            $entityManager->persist($Formations);
            $entityManager->flush();

            return $this->redirectToRoute('formulaire_communication', array('idLieu'=>$idLieu));
        }

        return $this->renderForm('formulaire/formation.twig',
            compact('formFormations',  'idLieu'));

    }



// 'state' permet simplement de se situer dans le controller ;
// Si state = 0, j'arrive dans le controller depuis la page précédente et j'effectue la création des entités Communication
// Si state = 'sub', je traite la donnée reçue depuis le form dans la vue

    #[Route('/communication/{idLieu}/{state}', name: '_communication', requirements: ['idLieu' => '\d+'])]
    public function communication(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                                  Request $request, $idLieu, TypeCommunicationRepository $typeCommunicationRepository, $state=0): Response
    {

        $lieu = $lieuRepository->find($idLieu);
        $Communication = $lieu->getCommunication();

        if($state === 0 ) {


            if ($Communication->count() === 0) {

                for ($i = 0, $iMax = count($typeCommunicationRepository->findAll()); $i < $iMax; $i++) {

                    $com = new Communication();
                    $com->setTypeCommunication($typeCommunicationRepository->find($i + 1));
                    $lieu->addCommunication($com);
                    $entityManager->persist($com);
                    $entityManager->flush();

                }

                $entityManager->persist($lieu);
                $entityManager->flush();
            }
        }

        if($state==='sub') {

            foreach($Communication as $com) {
                $nombre = $request->request->get('Nb'. $com->getTypeCommunication()->getId());
                $sujets = $request->request->get('Sujet' . $com->getTypeCommunication()->getId());
                $com->setNombre($nombre);
                $com->setSujets($sujets);
                $entityManager->persist($com);
                $entityManager->flush();
            }
            return $this->redirectToRoute('formulaire_atelier', array('idLieu'=>$idLieu));
        }



        return $this->render('formulaire/communication.twig',
            compact( 'Communication','idLieu'));
    }


    #[Route('/atelier/{idLieu}/{idAtelier}', name: '_atelier', requirements: ['idLieu' => '\d+'])]
    public function atelier(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, AtelierRepository $atelierRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                              Request $request, $idLieu, $idAtelier=0): Response
    {

        $lieu = $lieuRepository->find($idLieu);
        $ateliers = $lieu->getAtelier();
        $atelier = new Atelier();


        $formAteliers = $this->createForm(AteliersConsoType::class,$atelier);
        $formAteliers->handleRequest($request);


        if ($formAteliers->isSubmitted() && $formAteliers->isValid()) {

            $lieu->addAtelier($atelier);
            $entityManager->persist($atelier);
            $entityManager->flush();

            return $this->redirectToRoute('formulaire_atelier', array('idLieu'=>$idLieu));
        }

        if ($idAtelier !== 0){
            $atelier = $atelierRepository->find($idAtelier);
            $lieu->removeAtelier($atelier);
            $atelierRepository->remove($atelier);
            $entityManager->persist($lieu);
            $entityManager->flush();
        }

        return $this->renderForm('formulaire/ateliers.twig',
            compact('formAteliers', 'ateliers', 'idLieu'));

    }

    #[Route('/representation/{idLieu}/{state}', name: '_representation', requirements: ['idLieu' => '\d+'])]
    public function representation(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                                  Request $request, $idLieu, CategorieRepRepository $categorieRepRepository, $state=0): Response
    {

        $lieu = $lieuRepository->find($idLieu);
        $Representation = $lieu->getRepresentation();

        if($state === 0 ) {


            if ($Representation->count() === 0) {

                for ($i = 0, $iMax = count($categorieRepRepository->findAll()); $i < $iMax; $i++) {

                    $rep = new Representation();
                    $rep->setCategorie($categorieRepRepository->find($i + 1));
                    $lieu->addRepresentation($rep);
                    $entityManager->persist($rep);
                    $entityManager->flush();

                }

                $entityManager->persist($lieu);
                $entityManager->flush();
            }
        }

        if($state==='sub') {

            foreach($Representation as $rep) {
                $frequence = $request->request->get('frequence'. $rep->getCategorie()->getId());
                $rep->setFrequence($frequence);
                $entityManager->persist($rep);
                $entityManager->flush();
            }
            return $this->redirectToRoute('formulaire_actionJustice', array('idLieu'=>$idLieu));
        }



        return $this->render('formulaire/representation.html.twig',
            compact( 'Representation','idLieu'));
    }



    #[Route('/actionJustice/{idLieu}', name: '_actionJustice', requirements: ['idLieu' => '\d+'])]
    public function actionJustice(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                                   Request $request, $idLieu): Response
    {

        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getActionJustice() !== null) {
            $actionJustice = $lieu->getActionJustice();
        }
        else {
            $actionJustice = new ActionJustice();
        }

        $formActionJustice = $this->createForm(ActionsEnJusticeType::class,$actionJustice);
        $formActionJustice->handleRequest($request);

        if ($formActionJustice->isSubmitted() && $formActionJustice->isValid()) {
            $lieu->setActionJustice($actionJustice);
            $entityManager->persist($actionJustice);
            $entityManager->flush();

            return $this->redirectToRoute('formulaire_recap', array('idLieu'=>$idLieu));
        }

        return $this->renderForm('formulaire/actionJustice.html.twig',
            compact('formActionJustice',  'idLieu'));

    }

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

                    $nbSalaries += $sousLieu->getNbSalaries();
                    $nbBenevoles += $sousLieu->getNbBenevole();
                    $nbConsoTel += $sousLieu->getNbConsomRensTel();


                    //          Action en Justice
                    $nbAcc += $sousLieu->getActionJustice()->getNbAccompagnement();
                    $actionConjointes += $sousLieu->getActionJustice()->getNbActionConjointe();

                    //          Ateliers
                    $nbAteliers += count($sousLieu->getAtelier());
                    $ateliers = $sousLieu->getAtelier();

                    foreach ($ateliers as $atelier) {
                        $nbParti += $atelier->getNbPersonnesTotal();
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
                                $nb += $com->getNombre();
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
                                $nb += $dos->getNbDossiers();
                            }
                        }


                        $dossier->setTypologieDossier($typologieDossierRepository->find($i));
                        $dossier->setNbDossiers($nb);
                        $dossier->setLieu($lieu);

                        $entityManager->persist($dossier);
                        $entityManager->flush();
                    }

                    //          Formations
                    $nbFormationsAnnee += $sousLieu->getFormations()->getNbFormationsAnnee();
                    $themesFormation .= ', ' . $sousLieu->getFormations()->getThemeFormationEtParticipants();


                    //          Evenements
                    $events .= ', ' . $sousLieu->getEvenement()->getDetailEvenement();

                    //          Permanence
                    $nbHeures += $sousLieu->getPermanence()->getNbHeures();
                    $nbJours += $sousLieu->getPermanence()->getNbJours();
                    $nbDossierSimple += $sousLieu->getPermanence()->getNbDossierSimple();
                    $nbDossierDiff += $sousLieu->getPermanence()->getNbDossierDifficile();


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
                                $nb += $repi->getFrequence();
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

        return $this->render('formulaire/recap.html.twig',
            compact('lieu', 'Sections'));
    }

    #[Route('/transmission/{idLieu}', name: '_transmission', requirements: ['idLieu' => '\d+'])]
    public function transmission(EntityManagerInterface $entityManager, UserRepository $userRepository, StatutRepository $statutRepository, RepresentationRepository $representationRepository, CategorieRepRepository $categorieRepRepository, TypologieDossierRepository $typologieDossierRepository, DossierRepository $dossierRepository, CommunicationRepository $communicationRepository, TypeCommunicationRepository $typeCommunicationRepository, LieuRepository $lieuRepository, EchelleRepository $echelleRepository, UDRepository $UDRepository, $idLieu = 0): Response
    {
        $lieu = $lieuRepository->find($idLieu);

        if($lieu->getActionJustice() !== null && $lieu->getAtelier() !== null && $lieu->getCommunication() !== null && $lieu->getDossier() !== null && $lieu->getEvenement() !== null && $lieu->getFormations() !== null && $lieu->getPermanence() !== null && $lieu->getRepresentation() !== null ){
            $lieu->setStatut($statutRepository->find(2));
            $entityManager->persist($lieu);
            $entityManager->flush();
        }

        return $this->render('formulaire/transmission.html.twig',compact('idLieu'));
    }

}
