<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Permanence;
use App\Form\PermanencesType;
use App\Form\PresentationType;
use App\Repository\LieuRepository;
use App\Repository\StatutRepository;
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
            $formPresentation = $this->createForm(presentationType::class, $lieu);
            $formPresentation->handleRequest($request);

            if ($formPresentation->isSubmitted() && $formPresentation->isValid()) {

                $user = $userRepository->find($this->getUser()->getId());
                $lieu->setUser($user);
                $lieu->setStatut($statutRepository->find(1));

                $entityManager->persist($lieu);
                $entityManager->flush();

                return $this->redirectToRoute('formulaire_permanence', $idLieu);
            }

            return $this->renderForm('formulaire/presentation.html.twig',
                compact('formPresentation')
            );

        } else { //s'il existe déjà un lieu (ex : après avoir cliqué sur modifier au niveau des bilans US/Nationaux, ou quand on clique sur 'suivant')

            $lieu = $lieuRepository->find($idLieu);
            $formPresentation = $this->createForm(presentationType::class, $lieu);
            $formPresentation->handleRequest($request);

            if ($formPresentation->isSubmitted() && $formPresentation->isValid()) {

                $entityManager->persist($lieu);
                $entityManager->flush();

                return $this->redirectToRoute('formulaire_permanence', $idLieu);

            }
        }
        return $this->renderForm('formulaire/presentation.html.twig',
            compact('formPresentation')
        );
    }

    #[Route('/permanence/{idLieu}', name: 'permanence', requirements: ['idLieu' => '\d+'])]
    public function permanence(LieuRepository $lieuRepository, UDRepository $UDRepository, UserRepository $userRepository, StatutRepository $statutRepository, EntityManagerInterface $entityManager,
                               Request        $request, $idLieu = 0): Response
    {

//        dump($lieu);
            $Permanence = new Permanence();
            $formPermanence = $this->createForm(PermanencesType::class);
            $formPermanence->handleRequest($request);

            if ($formPermanence->isSubmitted() && $formPermanence->isValid()) {
                $lieu = $lieuRepository->find($idLieu);

                $entityManager->persist($Permanence);
                $entityManager->flush();

                return $this->redirectToRoute('home', $idLieu);
            }
            return $this->renderForm('formulaire/permanence.html.twig',
                compact('formPermanence'));

        }
}
