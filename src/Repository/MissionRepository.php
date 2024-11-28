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

    public function findInProgressMissionsToUpdate(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.statut = :status') // Missions "Commencées"
            ->andWhere('m.dateFin <= :now') // Date de fin dépassée
            ->setParameter('status', MissionStatus::IN_PROGRESS->value) // Utilisation de l'Enum
            ->setParameter('now', new \DateTimeImmutable())
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
