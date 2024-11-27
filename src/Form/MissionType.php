<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Mission;
use App\Entity\Pouvoir;
use App\Entity\MissionStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'label' => 'Titre de la mission',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => MissionStatus::cases(), // Récupère toutes les valeurs de l'enum
                'choice_label' => fn(MissionStatus $status) => $status->value, // Affiche la valeur textuelle
                'choice_value' => fn(?MissionStatus $status) => $status?->value, // Associe la valeur textuelle
            ])
            
        
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lieu', null, [
                'label' => 'Lieu de la mission',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('niveauDanger', null, [
                'label' => 'Niveau de danger (1-5)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('pouvoirsRequis', EntityType::class, [
                'class' => Pouvoir::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
            ])            
            ->add('equipeAssignee', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom', // Afficher le nom de l'équipe
                'placeholder' => 'Sélectionnez une équipe',
                'query_builder' => function (EntityRepository $repository) use ($options) {
                    // Optionnel : filtrer les équipes en fonction de leur état actif ou des pouvoirs requis
                    return $repository->createQueryBuilder('e')
                        ->where('e.estActive = true') // Par exemple, uniquement les équipes actives
                        ->orderBy('e.nom', 'ASC');
                },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}
