<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClearDatabaseController
{
    #[Route('/clear-database', name: 'clear_database')]
    public function clearDatabase(EntityManagerInterface $entityManager): Response
    {
        // Liste des entités à vider
        $repositories = [
            'App\Entity\Mission',
            'App\Entity\SuperHero',
            'App\Entity\Equipe',
            'App\Entity\Pouvoir',

        ];

        // Supprime toutes les entités
        foreach ($repositories as $entityClass) {
            $repository = $entityManager->getRepository($entityClass);
            $entities = $repository->findAll();
            foreach ($entities as $entity) {
                $entityManager->remove($entity);
            }
        }

        // Appliquer les suppressions
        $entityManager->flush();

        return new Response('Toutes les données ont été supprimées.');
    }
}
