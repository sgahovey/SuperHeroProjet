<?php

namespace App\DataTransformer;

use App\Entity\Equipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class EquipeToIdTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforme une entité Equipe en ID pour le formulaire.
     */
    public function transform($equipe): ?int
    {
        if (null === $equipe) {
            return null;
        }

        if (!$equipe instanceof Equipe) {
            throw new \InvalidArgumentException('Expected an instance of ' . Equipe::class);
        }

        return $equipe->getId();
    }

    /**
     * Transforme un ID reçu du formulaire en entité Equipe.
     */
    public function reverseTransform($equipeId): ?Equipe
{
    dump($equipeId); // Affichez ce qui est reçu
    if (!$equipeId) {
        return null;
    }

    // Convertissez l'ID en entier si nécessaire
    $equipeId = (int) $equipeId;

    $equipe = $this->entityManager->getRepository(Equipe::class)->find($equipeId);

    if (null === $equipe) {
        throw new \InvalidArgumentException(sprintf('Aucune équipe trouvée avec l\'ID %s.', $equipeId));
    }

    return $equipe;
}

    
}
