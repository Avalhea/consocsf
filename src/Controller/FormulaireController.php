<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Permanence;
use App\Form\PermanencesType;
use App\Form\PermanenceType;
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
                dump($user);
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

            $formPermanence = $this->createForm(PermanenceType::class);
            $formPermanence->handleRequest($request);

            if ($formPermanence->isSubmitted() && $formPermanence->isValid()) {
                dump($request->request->get('nbJour'));
                $lieu->setPermanence($permanence);
                $entityManager->persist($permanence);
                $entityManager->flush();

//                return $this->redirectToRoute('home', array('idLieu'=>$idLieu, 'caca'=>$formPermanence));
            }

            return $this->renderForm('formulaire/permanence.html.twig',
                compact('formPermanence',  'idLieu'));

        }










}
