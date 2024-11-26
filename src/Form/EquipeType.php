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

        if ($options['include_active']) {
            $builder->add('estActive', null, [
                'label' => 'Équipe active',
                'attr' => ['class' => 'form-check-input'],
            ]);
        }

        if ($options['include_chef']) {
            $builder->add('chef', EntityType::class, [
                'class' => SuperHero::class,
                'choice_label' => 'nom',
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('sh')
                        ->where('sh.niveauEnergie > :niveau')
                        ->andWhere('sh.estDisponible = true')
                        ->setParameter('niveau', 80)
                        ->orderBy('sh.nom', 'ASC');
                },
                'label' => 'Chef de l\'équipe',
                'placeholder' => 'Sélectionner un chef',
                'attr' => ['class' => 'form-control'],
            ]);
        }

        if ($options['include_membres']) {
            $builder->add('membres', EntityType::class, [
                'class' => SuperHero::class,
                'choice_label' => 'nom',
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('sh')
                        ->where('sh.estDisponible = true')
                        ->orderBy('sh.nom', 'ASC');
                },
                'multiple' => true,
                'expanded' => true,
                'label' => 'Membres de l\'équipe',
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
