<?php

namespace App\Controller;

use App\Entity\CategorieRep;
use App\Entity\TypeCommunication;
use App\Entity\TypologieDossier;
use App\Entity\User;
use App\Form\FormCatRepType;
use App\Form\FormTypeComType;
use App\Form\FormTypoDossierType;
use App\Form\RegistrationFormType;
use App\Repository\CategorieRepRepository;
use App\Repository\LieuRepository;
use App\Repository\RepresentationRepository;
use App\Repository\TypeCommunicationRepository;
use App\Repository\TypologieDossierRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/admin', name: 'admin_')]
class RegistrationController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/register', name: 'register')]
    public function register(
                            Request                     $request,
                            UserPasswordHasherInterface $userPasswordHasher,
                            EntityManagerInterface      $entityManager,
                            UserRepository              $userRepository,
    ): Response
    {
        // creation new user + recuperation des users
        $listeUser = $userRepository->findAll();
        $user = new User();

        // créer le formulaire d'ajout user
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // validation du formulaire, set du mot de passe par défaut et envoi à la BDD
        if ($form->isSubmitted() && $form->isValid()) {

            // Set du role du user
                $user->setPassword($userPasswordHasher->hashPassword(
                    $user, '1234'));
                if($user->getEchelle()->getId()== 1) {
                    $user->setRoles(["ROLE_SECTION"]);
                }
                if($user->getEchelle()->getId()== 2) {
                    $user->setRoles(["ROLE_UD"]);
                }
                if($user->getEchelle()->getId()== 3) {
                    $user->setRoles(["ROLE_NATIONAL"]);
                }

                //message flash de confirmation de compte -> a display sur la twig
            $this->addFlash('success', 'Le compte a bien été créé !');

                // on rentre les données sur la db
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_register');
        }
        return $this->renderForm('registration/register.html.twig',
            compact('listeUser', 'form'),
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/supprimeruser/{id}', name: 'supprimeruser')]
    public function supprimeruser(
        Request                         $request,
        EntityManagerInterface          $em,
        LieuRepository                  $lieuRepo,
        UserRepository                  $userRepo,
                                        $id

    ): Response

    {
        // Récupération du user à supprimer
        $userasupprimer = $userRepo->find($id);

        // si le user n'a pas de dossier en cours
        if($userasupprimer->getLieux() === null) {
            $this->addFlash('stop', 'Le user a bien été supprimé !');
            $em->remove($userasupprimer);
            $em->flush();
        }
        else {
            $this->addFlash('else', 'Le user a un dossier en cours et ne peut être supprimé. ');
        }
        return $this->redirectToRoute('admin_register');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/reinitialiserpassword/{id}', name: 'resetpassword')]
    public function resetpassword(
        Request                         $request,
        UserPasswordHasherInterface     $userPasswordHasher,
        EntityManagerInterface          $em,
        UserRepository                  $userRepository,
                                        $id

    ): Response

    {

        $user = $userRepository->find($id);
            $user->setPassword($userPasswordHasher->hashPassword($user, '1234'));

        $this->addFlash('reset', 'Le mot de passe a bien été reinitialisé !');
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin_register');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/ajoutrepresentation', name: 'ajouterrep')]
    public function gestionrep(
                                 Request                         $request,
                                 EntityManagerInterface          $em,
                                 CategorieRepRepository          $RepRepo,
    ): Response
    {
        // récupère la liste des representations + ajout
        $listeRep = $RepRepo->findAll();
        $nvRep = new CategorieRep();

        // créer le formulaire d'ajout representations
        $ajoutRep = $this->createForm(FormCatRepType ::class, $nvRep);
        $ajoutRep->handleRequest($request);

        // validation du formulaire et envoi à la BDD
        if ($ajoutRep->isSubmitted() && $ajoutRep->isValid()) {
            $this->addFlash('success', 'La représentation a bien été créé !');
            $em->persist($nvRep);
            $em->flush();

            // redirection vers la page de gestionnaire des representations
            return $this->redirectToRoute('admin_ajouterrep');
        }
        return $this->renderForm('registration/ajouterrep.html.twig',
            compact('listeRep', 'ajoutRep')
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/supprimerrepresentation/{id}', name: 'supprimerrepresentation')]
    public function supprimerrepresentation(
        Request                         $request,
        EntityManagerInterface          $em,
        CategorieRepRepository          $CatRepRepo,
        RepresentationRepository        $RepRepository,
                                        $id

    ): Response

    {
        // Récupération de la représentation à supprimer
        $categorieasupprimer = $CatRepRepo->find($id);
        $this->addFlash('stop', 'La représentation a bien été supprimé !');
        $em->remove($categorieasupprimer);
        $em->flush();

        return $this->redirectToRoute('admin_ajouterrep');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/ajouttypecom', name: 'ajoutertypecom')]
    public function gestiontypecom(
        Request                         $request,
        EntityManagerInterface          $em,
        TypeCommunicationRepository     $typecomRepo,
    ): Response
    {
        // récupère la liste des representations
        $listetypecom = $typecomRepo->findAll();

        // Ajout d'une nouvelle representations
        $nvtypecom = new TypeCommunication();

        // créer le formulaire d'ajout representations
        $ajouttypecom = $this->createForm(FormTypeComType ::class, $nvtypecom);
        $ajouttypecom->handleRequest($request);

        // validation du formulaire et envoi à la BDD
        if ($ajouttypecom->isSubmitted() && $ajouttypecom->isValid()) {
            $this->addFlash('success', 'Le type de communication a bien été créé !');
            $em->persist($nvtypecom);
            $em->flush();

            // redirection vers la page de gestionnaire des representations
            return $this->redirectToRoute('admin_ajoutertypecom');
        }
        return $this->renderForm('registration/ajoutcom.html.twig',
            compact('listetypecom', 'ajouttypecom')
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/supprimertypecom/{id}', name: 'supprimertypecom')]
    public function supprimertypecom(
        Request                         $request,
        EntityManagerInterface          $em,
        TypeCommunicationRepository     $typecomRepo,
                                        $id

    ): Response

    {
        // Récupération de la représentation à supprimer
        $typecomasupprimer = $typecomRepo->find($id);
        $this->addFlash('stop', 'Le type de communication a bien été supprimé !');
        $em->remove($typecomasupprimer);
        $em->flush();

        return $this->redirectToRoute('admin_ajoutertypecom');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/ajouttypodossier', name: 'ajouttypodossier')]
    public function gestiontypodossier(
        Request                         $request,
        EntityManagerInterface          $em,
        TypologieDossierRepository     $typodossierRepo,
    ): Response
    {
        // récupère la liste des representations
        $listetypodossier = $typodossierRepo->findAll();

        // Ajout d'une nouvelle representations
        $nvtypodossier = new TypologieDossier();

        // créer le formulaire d'ajout representations
        $ajouttypodossier = $this->createForm(FormTypoDossierType ::class, $nvtypodossier);
        $ajouttypodossier->handleRequest($request);

        // validation du formulaire et envoi à la BDD
        if ($ajouttypodossier->isSubmitted() && $ajouttypodossier->isValid()) {
            $this->addFlash('success', 'La typologie de dossier a bien été créé !');
            $em->persist($nvtypodossier);
            $em->flush();

            // redirection vers la page de gestionnaire des representations
            return $this->redirectToRoute('admin_ajouttypodossier');
        }
        return $this->renderForm('registration/ajouttypodossier.html.twig',
            compact('listetypodossier', 'ajouttypodossier')
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/supprimertypodossier/{id}', name: 'supprimertypodossier')]
    public function supprimertypodossier(
        Request                         $request,
        EntityManagerInterface          $em,
        TypologieDossierRepository      $typodossierRepo,
                                        $id = 0,

    ): Response

    {
        // Récupération de la représentation à supprimer
        $typodossierasupprimer = $typodossierRepo->find($id);
        $this->addFlash('stop', 'La typologie de dossier a bien été supprimé !');
        $em->remove($typodossierasupprimer);
        $em->flush();

        return $this->redirectToRoute('admin_ajouttypodossier');
    }

}
