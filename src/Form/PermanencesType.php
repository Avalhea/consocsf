<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Permanence;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermanencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nb_jours', EntityType::class,
                ["class"=>Permanence::class,
                    "choice_label"=>"name",
                    'label' => "Nombre de jours de permanence par an : "])

            ->add('nb_heures', EntityType::class,
                ["class"=>Permanence::class,
                    "choice_label"=>"name",
                    'label' => "Nombre d'heures de permanence par an : "])

            ->add('nb_dossier_simple', EntityType::class,
                ["class"=>Permanence::class,
                    "choice_label"=>"name",
                    'label' => "Nombre de dossiers consommation/ vie quotidienne simples "])

            ->add('nb_dossier_difficile', EntityType::class,
                ["class"=>Permanence::class,
                    "choice_label"=>"name",
                    'label' => "Nombre de dossiers consommation/ vie quotidienne difficiles "])
        ;


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
