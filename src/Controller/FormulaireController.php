<?php

namespace App\Controller;

use App\Entity\ActionJustice;
use App\Entity\Atelier;
use App\Entity\Communication;
use App\Entity\Dossier;
use App\Entity\DossiersAutre;
use App\Entity\Evenement;
use App\Entity\Formations;
use App\Entity\Lieu;
use App\Entity\Permanence;
use App\Entity\Representation;
use App\Form\ActionsEnJusticeType;
use App\Form\AteliersConsoType;
use App\Form\FormationsType;
use App\Form\PermanenceType;
use App\Form\PresentationType;
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
use App\Service\Verif;
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

//$user = $this->userRepository->find($this->getUser()->getId());
class FormulaireController extends AbstractController
{


    #[Route('/presentation/{idLieu}', name: '_presentation', requirements: ['idLieu' => '\d+'])]
    public function presentation(Verif $verif,LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, EchelleRepository $echelleRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                                 Request        $request, $idLieu = 0): Response
    {


        $user = $userRepository->find($this->getUser()->getId());
//        Appel du Service verif, et de sa fonction verification
//        La fonction verification renvoie 0 si jamais l'utilisateur n'est pas censé avoir accès à la page -> redirection sur home

        $verifi = $verif->verification($idLieu, $user);
// Si le user a bien accès à cette page >
        if ($verifi === "ok") {
// Si le paramètre 'idLieu' n'a pas été spécifié dans le path > idLieu = 0 > on veut instancier un nouveau 'lieu'
            if ($idLieu == 0) {
// création de lieu
                $lieu = new Lieu();
// création et instanciation du form
                $formPresentation = $this->createForm(PresentationType::class, $lieu);
                $formPresentation->handleRequest($request);
// Si le formulaire a bien été soumis et est valide >
                if ($formPresentation->isSubmitted() && $formPresentation->isValid()) {
// On récupère le user connecté
                    $user = $userRepository->find($this->getUser()->getId());
// On assigne à notre instance de lieu 'user' en tant que user > càd que l'utilisateur connecté est celui qui remplie le formulaire, et est donc rattaché à celui ci
                    $lieu->setUser($user);
// On set le 'statut' de 'lieu' en statut 1, càd en ' En cours '
                    $lieu->setStatut($statutRepository->find(1));
// Si l'échelle du User est 1, càd 'Section' >
                    if($user->getEchelle()->getId() == 1) {
//                        On set d'office l'échelle du lieu en 1, càd 'Section'
                        $lieu->setEchelle($echelleRepository->find(1));
                    }
                    else{
//                        On vérifie quelle échelle l'utilisateur a selectionné dans le form; 1>Section ou 2>UD  : Cette option n'est accessible que si l'utilisateur a une échelle d'ID 2 minimum ( UD ou National )
                        $ech = $request->request->get('echelle');
//                        Si 'Section' a été selectionné, on set l'échelle du lieu en Section (Echelle where ID=1)
                        if ($ech === 'Section') {
                            $lieu->setEchelle($echelleRepository->find(1));
                        }
                        // Si 'UD' a été selectionné, on set l'échelle du lieu en UD (Echelle where ID=2)
                        if ($ech === 'UD') {
                            $lieu->setEchelle($echelleRepository->find(2));
                        }

                    }

                    $entityManager->persist($lieu);
                    $entityManager->flush();
// redirection sur la page suivante > Permanence, en transmettant l'idLieu, qui est l'ID du lieu fraichement instancié
                    return $this->redirectToRoute('formulaire_permanence', array('idLieu' => $lieu->getId()));
                }
//                les variables ci dessous ne sont pas importantes quand il s'agit d'un nouveau formulaire
                $selectedUD = '';
                $selectedSec = '';
                return $this->renderForm('formulaire/presentation.html.twig',
                    compact('formPresentation','idLieu','selectedUD','selectedSec')
                );

            } else { //s'il existe déjà un lieu (ex : après avoir cliqué sur modifier au niveau des bilans US/Nationaux, ou quand on clique sur 'suivant')


                $lieu = $lieuRepository->find($idLieu);
                $formPresentation = $this->createForm(PresentationType::class, $lieu);
                $formPresentation->handleRequest($request);

                if ($formPresentation->isSubmitted() && $formPresentation->isValid()) {
                    if($user->getEchelle()->getId() == 1) {
                        $lieu->setEchelle($echelleRepository->find(1));
                    }
                    else{
                        $ech = $request->request->get('echelle');
                        if ($ech === 'Section') {
                            $lieu->setEchelle($echelleRepository->find(1));
                        }
                        if ($ech === 'UD') {
                            $lieu->setEchelle($echelleRepository->find(2));
                        }

                    }
                    $entityManager->persist($lieu);
                    $entityManager->flush();
                    dump($idLieu);
                    return $this->redirectToRoute('formulaire_permanence', array('idLieu' => $idLieu));

                }

//                Si l'échelle du 'lieu' sur lequel nous travaillons dans le form est set à '1'; soit Section >

                if($lieu->getEchelle()->getId() == 1){
//                    Ces variables sont utilisées dans la twig au niveau des options de mon 'select' echelle
//                    si l'échelle est à 1, alors je veux que le select soit pré-remplie par 'Section'
                    $selectedUD=' ';
                    $selectedSec='selected';
                }

//                Si l'échelle du 'lieu' sur lequel nous travaillons dans le form est set à '2' ou '3'; soit UD ou National (même si la deuxième option n'est pas possible) >

                else {
//                    Ces variables sont utilisées dans la twig au niveau des options de mon 'select' echelle
//                    si l'échelle est à 2, alors je veux que le select soit pré-remplie par 'UD'
                    $selectedUD='selected';
                    $selectedSec=' ';
                }

            }
//            render du form
            return $this->renderForm('formulaire/presentation.html.twig',
                compact('formPresentation','idLieu','selectedSec','selectedUD')
            );
        }
//        redirection sur 'home' si jamais les vérifications n'ont pas été concluentes
            return $this->redirectToRoute('home');
        }

    #[Route('/permanence/{idLieu}', name: '_permanence', requirements: ['idLieu' => '\d+'])]
    public function permanence(LieuRepository $lieuRepository, UDRepository $UDRepository, PermanenceRepository $permanenceRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                               Request $request, $idLieu): Response
    {

//        Instanciation du user connecté et du 'lieu' avec l'idLieu passé dans le paramètre du path
        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

//            Si jamais le user connecté n'est pas assigné au lieu qu'il essai de modifier dans le formulaire...
        if ($lieu->getUser() !== $user) {
//            ...Il est redirigé sur l'accueil
            return $this->redirectToRoute('home');
        }

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

        //        Instanciation du user connecté et du 'lieu' avec l'idLieu passé dans le paramètre du path

        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getUser() !== $user) {
            return $this->redirectToRoute('home');
        }
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

        //        Instanciation du user connecté et du 'lieu' avec l'idLieu passé dans le paramètre du path

        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getUser() !== $user) {
            return $this->redirectToRoute('home');
        }

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

        //        Instanciation du user connecté et du 'lieu' avec l'idLieu passé dans le paramètre du path


        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getUser() !== $user) {
            return $this->redirectToRoute('home');
        }

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

        //        Instanciation du user connecté et du 'lieu' avec l'idLieu passé dans le paramètre du path


        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getUser() !== $user) {
            return $this->redirectToRoute('home');
        }
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

        //        Instanciation du user connecté et du 'lieu' avec l'idLieu passé dans le paramètre du path


        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getUser() !== $user) {
            return $this->redirectToRoute('home');
        }
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

        //        Instanciation du user connecté et du 'lieu' avec l'idLieu passé dans le paramètre du path


        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getUser() !== $user) {
            return $this->redirectToRoute('home');
        }
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

        //        Instanciation du user connecté et du 'lieu' avec l'idLieu passé dans le paramètre du path


        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getUser() !== $user) {
            return $this->redirectToRoute('home');
        }

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
        //        Instanciation du user connecté et du 'lieu' avec l'idLieu passé dans le paramètre du path


        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getUser() !== $user) {
            return $this->redirectToRoute('home');
        }

//        On récupère la liste des Sections, soit tout les 'lieux' a échelle '1' (Section) dont le statut est à '2' (Verifié), appartenant à l'UD de lieu
        $Sections = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'UD' => $lieu->getUD(), 'statut' => $statutRepository->find(2)]);


        return $this->render('formulaire/recap.html.twig',
            compact('lieu', 'Sections'));
    }

    #[Route('/transmission/{idLieu}', name: '_transmission', requirements: ['idLieu' => '\d+'])]
    public function transmission(EntityManagerInterface $entityManager, UserRepository $userRepository, StatutRepository $statutRepository, RepresentationRepository $representationRepository, CategorieRepRepository $categorieRepRepository, TypologieDossierRepository $typologieDossierRepository, DossierRepository $dossierRepository, CommunicationRepository $communicationRepository, TypeCommunicationRepository $typeCommunicationRepository, LieuRepository $lieuRepository, EchelleRepository $echelleRepository, UDRepository $UDRepository, $idLieu = 0): Response
    {

        $user = $userRepository->find($this->getUser()->getId());
        $lieu = $lieuRepository->find($idLieu);

        if ($lieu->getUser() !== $user) {
            return $this->redirectToRoute('home');
        }
// Si toutes les étapes du formulaires ont bien étés remplies, et que 'lieu' a toute les données nécessaires, alors on set le statut de 'lieu' à '2' soit 'Verifié'
        if($lieu->getActionJustice() !== null && $lieu->getAtelier() !== null && $lieu->getCommunication() !== null && $lieu->getDossier() !== null && $lieu->getEvenement() !== null && $lieu->getFormations() !== null && $lieu->getPermanence() !== null && $lieu->getRepresentation() !== null ){
            $lieu->setStatut($statutRepository->find(2));
            $entityManager->persist($lieu);
            $entityManager->flush();
        }

        return $this->render('formulaire/transmission.html.twig',compact('idLieu'));
    }

}
