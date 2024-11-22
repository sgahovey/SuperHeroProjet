<?php

namespace App\Form;

use App\Entity\Pouvoir;
use App\Entity\SuperHero;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuperHeroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Ajout des champs pour l'entité superhero
        $builder
            ->add('nom')
            ->add('alterEgo')
            ->add('estDisponible')
            ->add('niveauEnergie')
            ->add('biographie')
            ->add('creeLe', null, [
                'widget' => 'single_text',
            ])
              // Champ pour associer les pouvoirs (relation ManyToMany avec l'entité Pouvoir)
              ->add('pouvoirs', EntityType::class, [
                'class' => Pouvoir::class, // Classe de l'entité associée
                'choice_label' => 'nom', // Ce qui sera affiché pour chaque choix (ici, le nom du pouvoir)
                'multiple' => true, // Permet de sélectionner plusieurs pouvoirs
                'expanded' => true, // Affiche une liste de cases à cocher
            ])
             // Ajout du champ pour l'upload d'image
            ->add('imageFile', FileType::class, [
                'label' => 'Télécharger une image', // Label pour le champ
                'required' => false, // Champ facultatif
                'mapped' => true, // Lien avec l'entité SuperHero
                'attr' => ['accept' => 'image/*'], // Restreint aux fichiers image
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SuperHero::class,
        ]);
    }

    
}
