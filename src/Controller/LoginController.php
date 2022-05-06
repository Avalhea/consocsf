<?php

namespace App\Controller;

use App\Form\MdpType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route("/", name:"")]
class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // Recuperer l'erreur d'authentification si il y a en une
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/check', name: 'check')]
    public function check(
        UserRepository $repoUser
    ): Response

    {
        // recupere le user authentifié
        $user = $this->getUser();
        // verificication si le mot de passe est toujours celui par defaut
        $isPassword1234 = password_verify('1234',$user->getPassword());

        // si toujours mdp par defaut alors redirection sur la twig dedié à la modif
        if($isPassword1234){
            return $this->redirectToRoute('mdp');
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @IsGranted("ROLE_SECTION")
     */
    #[Route('/mdp', name: 'mdp')]
    public function changeMdp(
        UserRepository $repository,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        // récupération du user en cours
        $user = $this->getUser();

        // creation du formulaire de modif du mdp pour le user authentifié
        $form = $this->createForm(MdpType::class, $user );
        $form->handleRequest($request);

        // Si formulaire est valide -> le nouveau mdp saisi par user est enregistré,
        // lié via id et hashé avant d'être envoyé à la db
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $mdpSimple = $user->GetPassword();
            $user->setPassword($passwordHasher->hashPassword($user,$mdpSimple));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }
        // erreur si les mdp entrés ne sont pas les mêmes
        $this->addFlash('error', ' Les deux mots de passes ne correspondent pas !');
        return $this->renderForm('home/mdp.html.twig', compact('form', 'user'));
    }
}
