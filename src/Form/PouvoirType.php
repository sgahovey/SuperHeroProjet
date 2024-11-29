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
        // Champ pour le nom du pouvoir
            ->add('nom', null, [ 
                'label' => 'Nom du Pouvoir',  // Label affiché dans le formulaire
                'attr' => ['class' => 'form-control'], // Ajoute des classes CSS Bootstrap
            ])
        // Champ pour la description du pouvoir
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control'],
            ])
        // Champ pour le niveau du pouvoir    
            ->add('niveau', null, [
                'label' => 'Niveau',
                'attr' => ['class' => 'form-control'],
            ])
        // Champ pour associer les super-héros (relation ManyToMany)
            ->add('superHeroes', EntityType::class, [
                'class' => SuperHero::class, // Classe de l'entité SuperHero liée
                'choice_label' => 'nom', // Affiche le champ "nom" des entités SuperHero dans la liste
                'multiple' => true, // Permet de sélectionner plusieurs super-héros
                'expanded' => true, // Affiche les choix sous forme de cases à cocher
                'label' => 'Super Héros associés :', // Label pour le champ
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
