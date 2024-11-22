<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Mission;
use App\Entity\SuperHero;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('estActive')
            ->add('creeLe', null, [
                'widget' => 'single_text',
            ])
            ->add('chef', EntityType::class, [
                'class' => SuperHero::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('membres', EntityType::class, [
                'class' => SuperHero::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true, // Cases à cocher
            ])
            ->add('missionActuelle', EntityType::class, [
                'class' => Mission::class,
                'choice_label' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
