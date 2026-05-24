<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private Connection $connection)
    {
        parent::__construct($registry, Event::class);
    }

    public function findPublished(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.published = true')
            ->orderBy('e.startsAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function vulnerableSearch(string $term): array
    {
        $sql = "SELECT * FROM event WHERE published = true AND (LOWER(title) LIKE LOWER('%".$term."%') OR LOWER(location) LIKE LOWER('%".$term."%')) ORDER BY starts_at ASC";
        return $this->connection->fetchAllAssociative($sql);
    }
}
