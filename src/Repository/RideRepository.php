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

    * @param string  $departure
    * @param string  $destination
    * @param \DateTimeInterface $date
    * @return Ride[]
    */
    public function findRidesByCriteria(string $departure, string $destination, \DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.departure = :departure')
            ->andWhere('r.destination = :destination')
            ->andWhere('r.date = :date')
            ->setParameter('departure', $departure)
            ->setParameter('destination', $destination)
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('r.departureDate', 'ASC')
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



