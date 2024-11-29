<?php

namespace App\Form;

use App\Entity\Mission;
use App\Entity\Pouvoir;
use App\Entity\MissionStatus;
use Symfony\Component\Form\AbstractType;
use App\DataTransformer\EquipeToIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class MissionType extends AbstractType
{
    private EquipeToIdTransformer $equipeToIdTransformer;

    public function __construct(EquipeToIdTransformer $equipeToIdTransformer)
    {
        $this->equipeToIdTransformer = $equipeToIdTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // Champ pour le titre de la mission
            ->add('titre', null, [
                'label' => 'Titre de la mission',
                'attr' => ['class' => 'form-control'],
            ])
        // Champ pour la description de la mission
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
        // Champ pour le statut de la mission (choix parmi les valeurs de l'enum MissionStatus)
            ->add('statut', ChoiceType::class, [
                'choices' => MissionStatus::cases(),
                'choice_label' => fn(MissionStatus $status) => $status->value, // Affiche la valeur de chaque statut
            ])
         // Champ pour la date de début
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text', // Affiche un champ de saisie unique (format ISO)
                'attr' => ['class' => 'form-control'],
            ])
        // Champ pour la localisation de la mission
            ->add('lieu', null, [
                'label' => 'Lieu de la mission',
                'attr' => ['class' => 'form-control'],
            ])
        // Champ pour le niveau de danger    
            ->add('niveauDanger', null, [
                'label' => 'Niveau de danger (1-5)',
                'attr' => ['class' => 'form-control'],
            ])
        // Champ pour associer les pouvoirs requis (relation ManyToMany)
            ->add('pouvoirsRequis', EntityType::class, [
                'class' => Pouvoir::class,
                'choice_label' => 'nom',  // Affiche le nom de chaque pouvoir
                'multiple' => true,  // Permet la sélection multiple
                'expanded' => true, // Affiche les choix sous forme de cases à cocher
                'label' => 'Pouvoirs Requis', // Label pour ce champ
            ])
        // Champ masqué pour l'équipe assignée
            ->add('equipeAssignee', HiddenType::class, [
                'required' => true,
            ]);  

         // Ajout du DataTransformer pour "equipeAssignee"
         $builder->get('equipeAssignee')->addModelTransformer($this->equipeToIdTransformer);
        }
    
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => Mission::class,
            ]);
        }
    }

