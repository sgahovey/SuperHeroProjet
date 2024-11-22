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
            ->add('nom')
            ->add('description')
            ->add('niveau')
            ->add('superHeroes', EntityType::class, [
                'class' => SuperHero::class,
                'choice_label' => 'nom', // Affiche le nom des Super Héros dans le champ
                'multiple' => true, // Permet la sélection multiple
                'required' => false, // Rend le champ facultatif
                'expanded' => false, // Utilise un select dropdown
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pouvoir::class,
        ]);
    }
}
