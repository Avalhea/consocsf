<?php

namespace App\Form;

use App\Entity\Echelle;
use App\Entity\UD;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'input is-rounded ', 'col-xs-2', "choice_label"=>"libelle",
                    'label' => " "],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un e-mail ?',
                    ]),
                ],
            ])

            ->add('echelle', EntityType::class,
                ["class"=>Echelle::class,
                    "choice_label"=>"libelle",
                    'label' => " "
                ])

            ->add('UD', EntityType::class,
                ["class"=>UD::class,
                    "choice_label"=>"libelle",
                    'label' => " "
                ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
