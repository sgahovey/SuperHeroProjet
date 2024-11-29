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
     * @param Equipe|null $equipe L'entité `Equipe` à transformer
     * @return int|null L'ID de l'équipe ou null si aucune équipe n'est définie
     */

    public function transform($equipe): ?int
    {
        // Si aucune entité n'est fournie, renvoyer null
        if (null === $equipe) {
            return null;
        }
        // Si l'objet fourni n'est pas une instance de `Equipe`, lever une exception
        if (!$equipe instanceof Equipe) {
            throw new \InvalidArgumentException('Expected an instance of ' . Equipe::class);
        }  
        // Retourne l'ID de l'entité
        return $equipe->getId();
    }

    /**
     * Transforme un ID reçu du formulaire en une entité `Equipe` (utilisé pour traiter les données du formulaire).
     *
     * @param int|string|null $equipeId L'ID reçu du formulaire
     * @return Equipe|null L'entité correspondante ou null si aucun ID n'est fourni
     * @throws \InvalidArgumentException Si aucun `Equipe` correspondant n'est trouvé
     */

    public function reverseTransform($equipeId): ?Equipe
    {
    // Debug : Affiche l'ID reçu pour déboguer si nécessaire
    dump($equipeId);
     // Si aucun ID n'est fourni, retourner null
    if (!$equipeId) {
        return null;
    }
    // Convertit l'ID en entier (au cas où il serait passé en tant que chaîne)
    $equipeId = (int) $equipeId;

     // Recherche l'entité `Equipe` correspondante dans la base de données
    $equipe = $this->entityManager->getRepository(Equipe::class)->find($equipeId);

    // Si aucune entité n'est trouvée, lever une exception
    if (null === $equipe) {
        throw new \InvalidArgumentException(sprintf('Aucune équipe trouvée avec l\'ID %s.', $equipeId));
    }
    // Retourne l'entité trouvée
    return $equipe;
    }

    
}
