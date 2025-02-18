<?php

namespace App\Form;

use App\Entity\ActionJustice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

class ActionsEnJusticeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbActionConjointe',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded is-focused', 'col-xs-2',
                'type' => 'int'
            ]])
            ->add('nbAccompagnement',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded', 'col-xs-2',
                'type' => 'int'
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActionJustice::class,
        ]);
    }
}
