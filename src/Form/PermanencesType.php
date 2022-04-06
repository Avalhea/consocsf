<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Permanence;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermanencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbJours',TextType::class, [
                'label' => ' ',
                'attr' => [
                    'class' => 'input is-rounded', 'col-xs-2',
                    'type' => 'int'
        ]
            ])

            ->add('nbHeures',TextType::class, [
                'label' => ' ',
                'attr' => [
                    'class' => 'input is-rounded', 'col-xs-2',
                    'type' => 'int'
                ]
            ])



            ->add('nbDossierSimple',TextType::class, [
                'label' => ' ',
                'attr' => [
                    'class' => 'input is-rounded', 'col-xs-2',
                    'type' => 'int'
                ]
            ])

            ->add('nbDossierDifficile',TextType::class, [
                'label' => ' ',
                'attr' => [
                    'class' => 'input is-rounded', 'col-xs-2',
                    'type' => 'int'
                ]
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PermanencesType::class,
        ]);
    }
}
