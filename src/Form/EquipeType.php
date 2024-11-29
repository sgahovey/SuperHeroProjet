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
            ->add('nom', null, [
                'label' => 'Nom de l\'équipe',
                'attr' => ['class' => 'form-control'],
            ]);
        // Ajout du champ "estActive" uniquement si l'option "include_active" est activée
        if ($options['include_active']) {
            $builder->add('estActive', null, [
                'label' => 'Équipe active',
                'attr' => ['class' => 'form-check-input'],
            ]);
        }
         // Ajout du champ "chef" uniquement si l'option "include_chef" est activée
        if ($options['include_chef']) {
            $builder->add('chef', EntityType::class, [
                'class' => SuperHero::class,
                'choice_label' => 'nom',
                'query_builder' => function ($repository) {  
        // Filtre les super-héros disponibles ayant un niveau d'énergie supérieur à 80
                    return $repository->createQueryBuilder('sh')
                        ->where('sh.niveauEnergie > :niveau')
                        ->andWhere('sh.estDisponible = true')
                        ->setParameter('niveau', 80)
                        ->orderBy('sh.nom', 'ASC');
                },
                'label' => 'Chef de l\'équipe', // Libellé du champ
                'placeholder' => 'Sélectionner un chef', // Valeur par défaut si aucun chef n'est sélectionné
                'attr' => ['class' => 'form-control'], // Classe CSS pour le style
            ]);
        }
        // Ajout du champ "membres" uniquement si l'option "include_membres" est activée
        if ($options['include_membres']) {
            $builder->add('membres', EntityType::class, [
                'class' => SuperHero::class,
                'choice_label' => 'nom',
                'query_builder' => function ($repository) {
                // Filtre les super-héros disponibles
                    return $repository->createQueryBuilder('sh')
                        ->where('sh.estDisponible = true')
                        ->orderBy('sh.nom', 'ASC');
                },
                'multiple' => true,  // Permet la sélection multiple (relation ManyToMany)
                'expanded' => true, // Affiche les choix sous forme de cases à cocher
                'label' => 'Membres de l\'équipe',  // Libellé du champ
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
            'include_active' => false,  // Inclure "estActive" dans certaines situations
            'include_chef' => false,    // Inclure "chef" dans certaines situations
            'include_membres' => false, // Inclure "membres" dans certaines situations
        ]);
    }
}
