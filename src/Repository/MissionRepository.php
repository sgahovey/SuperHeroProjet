<?php

namespace App\Repository;

use App\Entity\Mission;
use App\Entity\MissionStatus;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Mission>
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }


    public function findPendingMissionsToStart(\DateTimeImmutable $currentDate): array
{
    return $this->createQueryBuilder('m')
        ->where('m.statut = :status') // Filtre sur les missions "En attente"
        ->andWhere('m.dateDebut <= :now') // La date de début est dépassée
        ->setParameter('status', MissionStatus::PENDING)
        ->setParameter('now', $currentDate)
        ->getQuery()
        ->getResult();
}

    public function findInProgressMissionsToUpdate(\DateTimeImmutable $currentDate): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.statut = :status') // Filtre sur les missions "Commencées"
            ->andWhere('m.dateFin <= :now') // La date de fin est dépassée
            ->setParameter('status', MissionStatus::IN_PROGRESS)
            ->setParameter('now', $currentDate)
            ->getQuery()
            ->getResult();
    }
    

    


//    /**
//     * @return Mission[] Returns an array of Mission objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mission
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
