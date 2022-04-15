<?php

namespace App\Controller;

use App\Entity\ActionJustice;
use App\Entity\Atelier;
use App\Entity\Communication;
use App\Entity\Dossiers;
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
use App\Repository\LieuRepository;
use App\Repository\StatutRepository;
use App\Repository\TypeCommunicationRepository;
use App\Repository\UDRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/formulaire', name: 'formulaire')]
class FormulaireController extends AbstractController
{
    #[Route('/presentation/{idLieu}', name: '_presentation', requirements: ['idLieu' => '\d+'])]
    public function presentation(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                                 Request        $request, $idLieu = 0): Response
    {



        if ($idLieu == 0) {

            $lieu = new Lieu();
            $formPresentation = $this->createForm(PresentationType::class, $lieu);
            $formPresentation->handleRequest($request);

            if ($formPresentation->isSubmitted() && $formPresentation->isValid()) {

                $user = $userRepository->find($this->getUser()->getId());
//                dump($user);
                $lieu->setUser($user);
                $lieu->setStatut($statutRepository->find(1));

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
            $formPresentation = $this->createForm(presentationType::class, $lieu);
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
    public function permanence(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
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
            dump($permanence);
            $lieu->setPermanence($permanence);
            $entityManager->persist($permanence);
            $entityManager->flush();

            return $this->redirectToRoute('formulaire_typologiedossier', array('idLieu'=>$idLieu));
        }

        return $this->renderForm('formulaire/permanence.html.twig',
            compact('formPermanence',  'idLieu'));

    }

    #[Route('/typologiedossier/{idLieu}', name: '_typologiedossier', requirements: ['idLieu' => '\d+'])]
    public function dossier(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                            Request $request, $idLieu): Response
    {

        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getDossiers() !== null) {
            $TypologieDossier = $lieu->getDossiers();
        }
        else {
            $TypologieDossier = new Dossiers();
        }

        $formDossier = $this->createForm(DossierType::class,$TypologieDossier);
        $formDossier->handleRequest($request);

        if ($formDossier->isSubmitted() && $formDossier->isValid()) {
            $lieu->setDossiers($TypologieDossier);
            $entityManager->persist($TypologieDossier);
            $entityManager->flush();

            return $this->redirectToRoute('formulaire_vieAssociative', array('idLieu'=>$idLieu));
        }

        return $this->renderForm('formulaire/typologiedossier.twig',
            compact('formDossier',  'idLieu'));

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

        $formVieAssociative = $this->createForm(vieAssociativeType::class,$lieu);
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

                for ($i = 0; $i < count($typeCommunicationRepository->findAll()); $i++) {

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
            compact('formAteliers', 'ateliers',  'idLieu'));

    }



    #[Route('/representation/{idLieu}/{state}', name: '_representation', requirements: ['idLieu' => '\d+'])]
    public function representation(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                                  Request $request, $idLieu, CategorieRepRepository $categorieRepRepository, $state=0): Response
    {

        $lieu = $lieuRepository->find($idLieu);
        $Representation = $lieu->getRepresentation();

        if($state === 0 ) {


            if ($Representation->count() === 0) {

                for ($i = 0; $i < count($categorieRepRepository->findAll()); $i++) {

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

            if($lieu->getActionJustice() !== null && $lieu->getAtelier() !== null && $lieu->getCommunication() !== null && $lieu->getDossiers() !== null && $lieu->getEvenement() !== null && $lieu->getFormations() !== null && $lieu->getPermanence() !== null && $lieu->getRepresentation() !== null ){
                $lieu->setStatut($statutRepository->find(2));
                $entityManager->persist($lieu);
                $entityManager->flush();
            }


            return $this->redirectToRoute('home', array('idLieu'=>$idLieu));
        }

        return $this->renderForm('formulaire/actionJustice.html.twig',
            compact('formActionJustice',  'idLieu'));

    }



}
