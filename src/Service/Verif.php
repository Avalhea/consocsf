<?php

namespace App\Service;

use App\Repository\LieuRepository;
use App\Repository\StatutRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
//require_once 'dompdf/autoload.inc.php';
class Verif
{

    public function __construct(UserRepository $userRepository, StatutRepository $statutRepository, LieuRepository $lieuRepository){

        $this->userRepository = $userRepository;
        $this->statutRepository = $statutRepository;
        $this->lieuRepository = $lieuRepository;

    }

    public function verification($idLieu) {
        $user = $this->userRepository->find($this->getUser()->getId());
        if($this->lieuRepository->findOneBy(['user'=>$user,'statut'=>$this->statutRepository->find(2)])) {
            return $this->redirectToRoute('home');
        }
        $lieu = $this->lieuRepository->find($idLieu);
        if($idLieu !== 0) {
            if ($lieu->getUser() !== $user) {
                return $this->redirectToRoute('home');
            } //TODO A FINIR !!!!!
        }
    }

}