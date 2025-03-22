<?php

namespace App\Repository;

use App\Entity\Trajet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trajet>
 */
class TrajetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trajet::class);
    }


    public function findByRecherche(string $depart, string $arrivee, \DateTimeInterface $date): array
    {
        // On crée une plage de 00:00:00 à 23:59:59 pour couvrir toute la journée
        $startOfDay = (clone $date)->setTime(0, 0, 0);
        $endOfDay = (clone $date)->setTime(23, 59, 59);
    
        return $this->createQueryBuilder('t')
            ->andWhere('t.depart LIKE :depart')
            ->andWhere('t.arrivee LIKE :arrivee')
            ->andWhere('t.dateDepart BETWEEN :start AND :end')
            ->setParameter('depart', '%' . $depart . '%')
            ->setParameter('arrivee', '%' . $arrivee . '%')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->orderBy('t.dateDepart', 'ASC')
            ->getQuery()
            ->getResult();
    }
    






    //    /**
    //     * @return Trajet[] Returns an array of Trajet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Trajet
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
