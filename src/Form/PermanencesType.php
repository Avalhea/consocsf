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
            ->add('nbJours', EntityType::class,
                ["class"=>Permanence::class])

            ->add('nbHeures', EntityType::class,
                ["class"=>Permanence::class])

            ->add('nbDossierSimple', EntityType::class,
                ["class"=>Permanence::class,
                    "choice_label"=>"name"])

            ->add('nbDossierDifficile', EntityType::class,
                ["class"=>Permanence::class,
                    "choice_label"=>"name"]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PermanencesType::class,
        ]);
    }
}
