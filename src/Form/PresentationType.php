<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\UD;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresentationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('UD', EntityType::class,
                ["class"=>UD::class,
                    "choice_label"=>"libelle",
                    'label' => "UD : ",'mapped' => false])
            ->add('nom')
            ->add('adresse')
            ->add('joursEtHorairesOuverture')
;    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
