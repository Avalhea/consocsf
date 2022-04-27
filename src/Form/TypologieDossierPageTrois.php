<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Lieu;
use App\Entity\Permanence;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypologieDossierPageTrois extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nbSalaries',null,['label'=>' ',  'attr' => [
                'class' => 'is-rounded is-focused', 'col-xs-2',
                'type' => 'int'
            ]])
            ->add('nbBenevole',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded', 'col-xs-2',
                'type' => 'int'
            ]])
            ->add('nbConsomRensTel',null,['label'=>' ',  'attr' => [
                'class' => 'input is-rounded', 'col-xs-2',
                'type' => 'int'
            ]])
            ->add('detail_evenement', EntityType::class,
                ["class"=>Evenement::class,
                    'label' => " ",  'attr' => [
                    'class' => 'input is-rounded', 'col-xs-2',
                    'type' => 'int']])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
