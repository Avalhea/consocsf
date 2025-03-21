<?php

namespace App\Form;

use App\Entity\Representation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepresentationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('frequence',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded is-focused', 'col-xs-2',
                'type' => 'int'
            ]])
            ->add('categorie',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded', 'col-xs-2',
                'type' => 'int'
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Representation::class,
        ]);
    }
}
