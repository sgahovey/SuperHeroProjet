<?php

namespace App\Form;

use App\Entity\Pouvoir;
use App\Entity\SuperHero;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PouvoirType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom du Pouvoir',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('niveau', null, [
                'label' => 'Niveau',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('superHeroes', EntityType::class, [
                'class' => SuperHero::class,
                'choice_label' => 'nom', // Affiche le nom des super-héros
                'multiple' => true, // Autorise plusieurs choix
                'expanded' => true, // Affiche les choix sous forme de cases à cocher
                'label' => 'Super Héros associés :',
                'attr' => ['class' => 'form-check-inline'], // Ajout de classe pour styliser les checkboxes
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pouvoir::class,
        ]);
    }
}
