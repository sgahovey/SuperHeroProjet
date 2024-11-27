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
            ->add('titre', null, [
                'label' => 'Titre de la mission',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => MissionStatus::cases(),
                'choice_label' => fn(MissionStatus $status) => $status->value,
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
                'label' => 'Pouvoirs Requis',
            ])
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

