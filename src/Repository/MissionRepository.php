<?php

namespace App\Repository;

use App\Entity\Mission;
use App\Entity\MissionStatus;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Mission>
 */
// Le constructeur initialise le repository en liant la classe Mission à Doctrine.
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

/**
     * Trouve les missions en attente ("PENDING") qui doivent être commencées.
     * Une mission est considérée comme devant commencer si :
     * - Son statut est "EN ATTENTE" (PENDING).
     * - Sa date de début est égale ou inférieure à la date actuelle.
     * 
     * @param \DateTimeImmutable $currentDate La date actuelle pour comparer les missions.
     * @return array Liste des missions en attente prêtes à être commencées.
 */
    public function findPendingMissionsToStart(\DateTimeImmutable $currentDate): array
{
    return $this->createQueryBuilder('m')  // Création d'un QueryBuilder pour la table "Mission" aliasée en "m".
        ->where('m.statut = :status') // Filtre sur les missions "En attente"
        ->andWhere('m.dateDebut <= :now') // Filtre : la date de début est dépassée ou égale à "now".
        ->setParameter('status', MissionStatus::PENDING) // Définit la valeur du paramètre "status".
        ->setParameter('now', $currentDate) // Définit la valeur du paramètre "now".
        ->getQuery() // Génère la requête.
        ->getResult(); // Exécute la requête et retourne le résultat.
}

/**
     * Trouve les missions commencées ("IN_PROGRESS") qui doivent être mises à jour.
     * Une mission est considérée comme devant être mise à jour si :
     * - Son statut est "COMMENCÉE" (IN_PROGRESS).
     * - Sa date de fin est dépassée (inférieure ou égale à la date actuelle).
     * 
     * @param \DateTimeImmutable $currentDate La date actuelle pour comparer les missions.
     * @return array Liste des missions commencées prêtes à être mises à jour.
 */

    public function findInProgressMissionsToUpdate(\DateTimeImmutable $currentDate): array
    {
        return $this->createQueryBuilder('m') // Création d'un QueryBuilder pour la table "Mission" aliasée en "m".
            ->where('m.statut = :status') // Filtre sur les missions "Commencée"
            ->andWhere('m.dateFin <= :now') // Filtre : la date de fin est dépassée ou égale à "now".
            ->setParameter('status', MissionStatus::IN_PROGRESS)// Définit la valeur du paramètre "status".
            ->setParameter('now', $currentDate) // Définit la valeur du paramètre "now".
            ->getQuery() // Génère la requête.
            ->getResult(); // Exécute la requête et retourne le résultat.
    }
}
