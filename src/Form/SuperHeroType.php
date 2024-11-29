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
        // Ajout des champs de l'entité superhero
        $builder
            ->add('nom')
            ->add('alterEgo')
            ->add('estDisponible')
            ->add('niveauEnergie')
            ->add('biographie')
            ->add('creeLe', null, [
                'widget' => 'single_text',
            ])
              // Champ pour associer des pouvoirs au super-héros (relation ManyToMany avec Pouvoir)
              ->add('pouvoirs', EntityType::class, [
                'class' => Pouvoir::class, // Classe de l'entité associée
                'choice_label' => 'nom', // Ce qui sera affiché pour chaque choix (ici, le nom du pouvoir)
                'multiple' => true, // Permet de sélectionner plusieurs pouvoirs
                'expanded' => true, // Affiche une liste de cases à cocher
            ])
             //  Champ pour l'upload d'une image (non directement mappé à la base de données)
            ->add('imageFile', FileType::class, [
                'label' => 'Télécharger une image', // Texte du label
                'required' => false, // Rend le Champ facultatif
                'mapped' => true, // Indique que ce champ est lié à une propriété de l'entité SuperHero
                'attr' => ['accept' => 'image/*'], // Filtre pour accepter uniquement les fichiers image
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
