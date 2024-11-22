<?php

namespace App\Form;

use App\Entity\Pouvoir;
use App\Entity\SuperHero;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuperHeroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('alterEgo')
            ->add('estDisponible')
            ->add('niveauEnergie')
            ->add('biographie')
            ->add('nomImage')
            ->add('creeLe', null, [
                'widget' => 'single_text',
            ])
            ->add('pouvoirs', EntityType::class, [
                'class' => Pouvoir::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SuperHero::class,
        ]);
    }
}
