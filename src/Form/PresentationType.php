<?php

namespace App\Form;

use App\Entity\Echelle;
use App\Entity\Lieu;
use App\Entity\UD;
use Doctrine\ORM\Mapping\Entity;
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
                    'label' => " "
                    ])
            ->add('echelle', EntityType::class,
                ["class"=>Echelle::class,
                    "choice_label"=>"libelle",
                    'label' => " "
                ])
            ->add('nom',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded is-focused', 'col-xs-2',
                'type' => 'int'
            ]])
            ->add('adresse',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded', 'col-xs-2',
                'type' => 'int'
            ]])
            ->add('joursEtHorairesOuverture',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded', 'col-xs-2',
                'type' => 'int'
            ]])
;    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
