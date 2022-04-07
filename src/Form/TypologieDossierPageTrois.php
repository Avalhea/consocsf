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

            ->add('nbSalaries')
            ->add('nbBenevole')
            ->add('nbConsomRensTel')
            ->add('detail_evenement', EntityType::class,
                ["class"=>Evenement::class,
                    'label' => " "])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
