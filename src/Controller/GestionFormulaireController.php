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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/gestion/formulaire', name: 'gestion_formulaire')]
class GestionFormulaireController extends AbstractController
{
    #[Route('/recap/{idLieu}/{Redirection}', name: '_recap', requirements: ['idLieu' => '\d+'])]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository, StatutRepository $statutRepository, RepresentationRepository $representationRepository, CategorieRepRepository $categorieRepRepository, TypologieDossierRepository $typologieDossierRepository, DossierRepository $dossierRepository, CommunicationRepository $communicationRepository, TypeCommunicationRepository $typeCommunicationRepository, LieuRepository $lieuRepository, EchelleRepository $echelleRepository, UDRepository $UDRepository, $idLieu = 0, $Redirection = 0): Response
    {
//        On compte le nombre de 'lieu' à échelle '2' (UD) ; Si ce nombre est inferieur au nombre de départements en BDD, on instancie autant d'objets 'Lieu' qu'il n'y a de départements
        if (count($lieuRepository->findBy(['echelle' => '2'])) < count($UDRepository->findAll())) {
//            On boucle sur le nombre de départements existants en BDD
            for ($i = 1; $i < count($UDRepository->findAll()) + 1; $i++) {
//               Instanciation de lieu
                $ud = new Lieu();
//               On set l'échelle = Echelle where 'id=2' = 'UD'
                $ud->setEchelle($echelleRepository->find(2));
//               On set l'UD (le département) sur le département à ID=$i, $i étant le nombre de fois qu'on a bouclé
//                ex : Si $i = 1, alors UD= UD where id=1 = 'AIN'
                $ud->setUD($UDRepository->find($i));
//               On set le nom du lieu sur le nom du département; ex: 'AIN' si $i = 1;
                $ud->setNom($UDRepository->find($i)->getLibelle());
                $entityManager->persist($ud);
                $entityManager->flush();
            }
        }

//        ! Nettoyage de la BDD, si jamais un user à échelle UD ou superieur remplie le formulaire à echelle 'UD', pour que cette entrée prenne le pas sur le lieu UD correspondant auto généré par le controller

//        On récupère la liste des départements
        $listeDepartements = $UDRepository->findAll();

//        On boucle sur chaque département
        foreach($listeDepartements as $departement) {
// On récupère le ou les lieux à échelle 2; les UDS, appartenant au département sur lequel on se trouve dans la boucle
            $LesUds = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(2),'UD'=>$departement]);

//            Si nous avons récupéré plus de deux lieu sur la ligne précedente, c'est que nous devons effacer le lieu auto généré par le controller
            if (count($LesUds)>1) {

//                Le lieu à effacer est le lieu à echelle 'UD' appartenant au département correspondant, et qui a un 'statut' null;
//                En effet; si le user remplie le formulaire pour son UD, alors le statut de ce lieu sera set sur 'EC' ou 'VAL'
//                c'est comme ça que nous les différencions

                $UdsAEffacer = $lieuRepository->findBy(['UD'=>$departement,'echelle'=>$echelleRepository->find(2),'statut'=>null]);

//                On efface toutes les données restantes liées au lieu, pour être en mesure de le retirer de la BDD
                    foreach ($UdsAEffacer as $UDAEffacer) {
                        foreach ($UDAEffacer->getRepresentation() as $Rep){
                            $entityManager->remove($Rep);
                        }
                        foreach ($UDAEffacer->getDossier() as $Dos){
                            $entityManager->remove($Dos);
                        }
                        foreach ($UDAEffacer->getCommunication() as $Com){
                            $entityManager->remove($Com);
                        }
//                        On retire le lieu (l'UD) à effacer, de la BDD
                        $entityManager->remove($UDAEffacer);
                        $entityManager->flush();
                    }


            }

        }


// On génère un 'lieu' à échelle 3, soit 'National' s'il n'existe pas déjà : On set son nom sur National, et son échelle sur 3.
        if (count($lieuRepository->findBy(['echelle' => '3'])) == 0) {
                $national = new Lieu();
                $national->setEchelle($echelleRepository->find(3));
                $national->setNom('National');
                $entityManager->persist($national);
                $entityManager->flush();
            }


//        Nous allons effectuer tout les calculs nécessaires :
//        Pour chaque UD : On veut additioner les données de toute ses Sections
//        Pour le National : On veut additioner les données de toutes les UDS


//      On boucle deux fois : Pour effectuer les mêmes calculs à échelle UD, puis à échelle National
        for($j=0;$j<2;$j++) {



            if($j==0) {
//            Si boucle = 0 ; La liste des lieux sera constituée de tout les lieux à échelle 2, dont le statut est null:
//             Nous ne voulons pas effectuer les calculs sur les UDS qui ont été remplies à la main par l'utilisateur dans le formulaire
                $lieux = $lieuRepository->findBy(['echelle' => $echelleRepository->find(2),'statut'=>null]);
            }
            else if($j==1) {
//              Si boucle = 1; la "liste" sera contituée de l'unique lieu à échelle National
                $lieux = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(3)]);
            }

            foreach ($lieux as $lieu) {
//                On boucle pour chaque UD dans la liste de lieux(d'UDs) ou une fois pour le lieu à échelle National

                if($j==0) {
//                    Si nous sommes sur les calculs à échelle UD, les "sous lieux" qui seront additionnées sont les Sections qui constitue l'UD
                    $sousLieux = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'UD' => $lieu->getUD(), 'statut' => $statutRepository->find(2)]);
                }
                else if($j==1) {
//                    Si nous sommes sur les calculs à échelle National, les "sous lieux" qui seront additionnées sont les UDs

                    $sousLieux = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(2)]);
                }

// instanciation de certaines variables
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


// Pour chaque 'Sous Lieu' de notre 'Lieu'

//                ex: Pour chaque Section de notre UD
                foreach ($sousLieux as $sousLieu) {

//                    On additionne le nombre de Salariés/bénevoles/.. à chaque boucle
                    $nbSalaries += $sousLieu->getNbSalaries();
                    $nbBenevoles += $sousLieu->getNbBenevole();
                    $nbConsoTel += $sousLieu->getNbConsomRensTel();


                    //          Action en Justice
                    $nbAcc += $sousLieu->getActionJustice()->getNbAccompagnement();
                    $actionConjointes += $sousLieu->getActionJustice()->getNbActionConjointe();



                    //          Ateliers

//                    Si nous calculons à échelle UD :

                    if($j==0) {
//                         on récupère tout les ateliers de la Section
                        $ateliers = $sousLieu->getAtelier();

//                        le nombre d'ateliers total de l'UD est le nombre d'atelier dans la liste 'ateliers' > On additionne ce nombre pour chaque Section
                        $nbAteliers += count($ateliers);
// On parcourt tout les ateliers de la Section
                    foreach ($ateliers as $atelier) {
//                        On additionne le nombre de participants de chaque Atelier
                        $nbParti += $atelier->getNbPersonnesTotal();
                        }
                    }

//                    à échelle Nationale

                    if($j==1){
// Si le sous lieu est une UD auto générée par le controller : càd qui n'a pas d'Atelier, mais un nbAtelier et un nbParticipants dans la table 'Lieu' :

                        if ($sousLieu->getStatut() == null) {
// On additionne simplement le nbAteliers & nbParti pour chaque UD
                            $nbAteliers += $sousLieu->getNbAteliers();
                            $nbParti += $sousLieu->getNbPartiAteliers();

                        }

//  Si le sous lieu est une UD générée par le form, càd qui a un ou plusieurs instances d'Ateliers qui lui sont liées
                        else {
//                            On récupère la liste d'Ateliers liés à l'UD (sous lieu)
                            $ateliers = $sousLieu->getAtelier();
//                            On compte le nombre d'Atelier dans cette liste; on additionne ce nombre au nb d'ateliers déjà calculés
                            $nbAteliers += count($ateliers);
//                            On boucle dans la liste d'ateliers de l'UD (sous lieu)
                            foreach ($ateliers as $atelier) {
//                            On récupère le nombre de participants pour chaque atelier, que l'on additionne au nb de participants déjà calculés
                                $nbParti += $atelier->getNbPersonnesTotal();
                            }
                        }
                    }


                    //          Communication

//                    On boucle autant de fois qu'il n'y a de types de communication en BDD
                    for ($i = 1; $i < count($typeCommunicationRepository->findAll()) + 1; $i++) {
                        $nb = 0;

//                    On génère autant d'objets 'Communication' qu'il n'y a de typeCommunication !!Si il n'en existe pas déjà associé au LIEU!!
                        if (!$communicationRepository->findOneBy(['lieu' => $lieu, 'typeCommunication' => $typeCommunicationRepository->find($i)])) {
                            $communication = new Communication();
                        } else {
//                            On récupère sinon l'instance de communication correspondant au typeCommunication dans lequel on se trouve dans la boucle $i
                            $communication = $communicationRepository->findOneBy(['lieu' => $lieu, 'typeCommunication' => $typeCommunicationRepository->find($i)]);
                        }

//                      On boucle pour chaque sousLieu  (chaque Section ou chaque UD)
                        foreach ($sousLieux as $sousL) {
//                      On récupère sinon l'instance de communication correspondant au typeCommunication dans lequel on se trouve dans la boucle $i

                            $com = $communicationRepository->findOneBy(['lieu' => $sousL, 'typeCommunication' => $typeCommunicationRepository->find($i)]);
//                            Si l'instance existe bien>
                            if ($com) {
//                            On récupère le nombre associé à cette instance de Communication
                                $nb += $com->getNombre();
                            }
                        }

//                        On set le type de communication de notre nouvelle instance de communication sur le type correspondant à la boucle
                        $communication->setTypeCommunication($typeCommunicationRepository->find($i));
//                        On set le nb de communication de l'instance de Communication sur 'nb' > nb étant le total de nombre de communications associé à ce type de comm' pour chaque sousLieu
                        $communication->setNombre($nb);
//                        On set le lieu de l'objet communication sur notre Lieu (UD ou National)
                        $communication->setLieu($lieu);
                        $entityManager->persist($communication);
                        $entityManager->flush();

                    }

                    //          Dossier
                    //          CF communication
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
                    //          CF communication


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
//                S'il n'existe pas d'instance d'ActionJustice associée au Lieu, on en crée une, sinon; on récupère celle existante
                if (!$lieu->getActionJustice()) {
                    $actionJustice = new ActionJustice();
                } else {
                    $actionJustice = $lieu->getActionJustice();
                }
//               On set chaque colonne de la table Action en Justice avec les données calculées au préalable dans nos boucles
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

//  On lie nos différentes instances : Formation/Evenement/Permanence... à Lieu (UD ou National)
                $lieu->setFormations($formation)->setEvenement($evenement)->setPermanence($permanence)->setNbSalaries($nbSalaries)->setNbBenevole($nbBenevoles)->setNbConsomRensTel($nbConsoTel);

                $entityManager->persist($lieu);
                $entityManager->flush();
            }
        }

// Préparation  des variables à render selon l'utilisateur connecté et le paramètre idLieu

            $user = $userRepository->find($this->getUser()->getId());

//        si l'idLieu n'a pas été spécifié dans le path, càd idLieu = 0
            if ($idLieu == 0) {
//                Lieu est alors l'UD (lieu) associé à l'UD (département) de l'utilisateur connecté
                $lieu = $lieuRepository->findOneBy(['echelle' => $echelleRepository->find(2), 'UD' => $user->getUd()]);
            } else {
                $lieu = $lieuRepository->find($idLieu);
                if ($user->getEchelle()->getId() !== 3) {
//                    Si l'utilisateur n'a pas l'échelle National, et n'est pas associé à l'UD du lieu OU qu'il a une échelle Section, alors il est redirigé vers l'accueil
                    if ($user->getUd() !== $lieu->getUD() || $user->getEchelle()->getId() == 1) {
                        return $this->redirectToRoute('home');
                    }
                }
            }
//            On vérifie que $lieu existe, pour éviter les pointer null exception
            if($lieu !== null) {
//                Si l'échelle du lieu n'est pas l'échelle Nationale
                if ($lieu->getEchelle()->getId() < 3) {
//                    On récupère la liste de Sections qui correspond ici à toute les sections (lieu where echelle.id = 1) appartenant à l'UD du lieu, et ayant un statut '2' (validé, pour ne prendre en compte que les lieux qui ont été duement remplie dans le formulaire)
                    $Sections = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'UD' => $lieu->getUD(), 'statut' => $statutRepository->find(2)]);
                } else {
//                    Pour l'échelle national, on récupère simplement TOUTES les sections au statut = 2;
                    $Sections = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'statut' => $statutRepository->find(2)]);
                }
            }

            else {
                $Sections = 0;
            }

//            Si on arrive ici par le path donnant un paramètre 'yes' à redirection, on renvoie le user sur le path _recapTableaux du Controller > Je fais ça pour recalculer en permanence les données
            if($Redirection === 'yes') {
                return $this->redirectToRoute('gestion_formulaire_recapTableaux');
            }

            return $this->render('recap/recap.html.twig',
                compact('lieu', 'Sections'));
    }

    #[Route('/recapitulatif', name: '_recapTableaux', requirements: ['id' => '\d+'])]
    public function tableaux(LieuRepository $lieuRepository, UserRepository $userRepository ,StatutRepository $statutRepository, EchelleRepository $echelleRepository,$id = 0):Response
    {

//        On récupère le user connecté
        $user = $userRepository->find($this->getUser()->getId());

//        Si l'échelle du user = 2; càd échelle UD, on veut générer la page 'recap/tableau/ud.html.twig'
        if($user->getEchelle()->getId() === 2) {
//            on récupère son UD associée
            $UD = $lieuRepository->findOneBy(['echelle'=>$echelleRepository->find(2),'UD'=>$user->getUd()]);
//            Si l'UD existe et que son statut == null, càd que l'UD est générée par le controller et donc calculée par la machine et de ce fait pas modifiable par le user directement ET n'a pas de sous lieu Sections
            if($UD !== null && $UD->getStatut() == null) {
//                On récupère les Sections
                $Sections = $lieuRepository->findBy(['echelle' => $echelleRepository->find(1), 'UD' => $UD->getUD()]);
            }
            else {
//                si le bilan de l'UD a été remplie dans le formulaire par le user qui a fait ses calculs à la main, alors l'UD (lieu) en question n'a pas de Sections
                $Sections = '';
            }
            return $this->render('recap/tableau/ud.html.twig',
                compact('UD', 'Sections'));
        }

//        Si le user a une échelle national, on veut générer la page 'recap/tableau/national.html.twig'
        if($user->getEchelle()->getId() == 3){
//            On récupère le lieu à échelle National..
            $National = $lieuRepository->findOneBy(['echelle'=>$echelleRepository->find(3)]);
//            Et ses UDS
            $UDs = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(2)]);
//            ainsi que toutes les sections
            $Sections = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(1)]);
            return $this->render('recap/tableau/national.html.twig',
                compact('National','UDs', 'Sections'));
        }
        return $this->redirectToRoute('home');
    }

    #[Route('/horaires/ouverture', name: '_horairesOuverture')]
    public function horaires(PdfService $pdf,LieuRepository $lieuRepository,StatutRepository $statutRepository, EchelleRepository $echelleRepository,$id = 0)
    {
//        On récupère les UDS et sections
    $sections = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(1)]);
    $uds = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(2)]);
    return $this->render('recap/horaires.html.twig',
        compact('sections','uds'));
    }

    #[Route('/horaires/pdf', name: '_horairesPDF')]
    public function generatePdfHoraires(PdfService $pdf,LieuRepository $lieuRepository,StatutRepository $statutRepository, EchelleRepository $echelleRepository,$id = 0)
    {
        $sections = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(1)]);
        $uds = $lieuRepository->findBy(['echelle'=>$echelleRepository->find(2)]);

        $html = $this->render('recap/horaires.html.twig',
            compact('sections','uds'));;


        $nom = 'HorairesJoursOuvertureSections.PDF';
        $pdf->showPdfFile($html, $nom);
    }


    #[Route('/pdf/{id}', name: '_detailBilanPdf', requirements: ['id' => '\d+'])]
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
