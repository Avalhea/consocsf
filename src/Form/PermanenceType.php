<?php

namespace App\Form;

use App\Entity\Permanence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermanenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbJours',null,['label'=>' ',  'attr' => [
        'class' => 'input is-rounded', 'col-xs-2',
        'type' => 'int'
    ]])
            ->add('nbHeures',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded', 'col-xs-2',
                'type' => 'int'
            ]])
            ->add('nbDossierSimple',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded', 'col-xs-2',
                'type' => 'int'
            ]])
            ->add('nbDossierDifficile',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded', 'col-xs-2',
                'type' => 'int'
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Permanence::class,
        ]);
    }
}
