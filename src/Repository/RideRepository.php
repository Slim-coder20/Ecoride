<?php

namespace App\Repository;

use App\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ride>
 */
class RideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ride::class);
    }

    /**
     * Recherche des trajets correspondant aux critères de depart et de destination et de la date
     *
     * @param string $departure
     * @param string $destination
     * @param \DateTimeInterface $date
     * @return Ride[]
     */
    public function findRidesByCriteria(string $departure, string $destination, \DateTimeInterface $date): array
    {
        // Début de la journée
        $startDate = (clone $date)->setTime(0, 0, 0);
        // Fin de la journée
        $endDate = (clone $date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('r')
            ->andWhere('r.departureCity = :departure')
            ->andWhere('r.destinationCity = :destination')
            ->andWhere('r.departureDate BETWEEN :startDate AND :endDate') // Comparaison sur la plage de dates
            ->andWhere('r.remainingSeats > 0') // Trajets avec des places disponibles
            ->setParameter('departure', $departure)
            ->setParameter('destination', $destination)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('r.departureDate', 'ASC') // Trier par date de départ
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des trajets les plus proches de la date de départ
     * @param \DateTimeInterface $date
     * @return Ride[]
     */
    public function findClosestRides(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.departureDate > :date')
            ->andWhere('r.remainingSeats > 0')
            ->setParameter('date', $date->format('Y-m-d H:i:s'))
            ->orderBy('r.departureDate', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}



