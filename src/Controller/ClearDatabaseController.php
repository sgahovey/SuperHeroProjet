<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClearDatabaseController extends AbstractController
{
    #[Route('/clear-database', name: 'clear_database')]
    public function clearDatabase(EntityManagerInterface $entityManager): Response
    {
        // Liste des entités à supprimer
        $repositories = [
            'App\Entity\Mission',
            'App\Entity\SuperHero',
            'App\Entity\Equipe',
        ];

        foreach ($repositories as $entityClass) {
            $repository = $entityManager->getRepository($entityClass);
            $entities = $repository->findAll();

            foreach ($entities as $entity) {
                $entityManager->remove($entity);
            }
        }

        $entityManager->flush();

        return new Response('Toutes les données ont été supprimées.');
    }
}
