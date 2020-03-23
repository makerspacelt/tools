<?php

namespace App\Repository;

use App\Entity\ToolTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class TagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ToolTag::class);
    }

    public function searchTags($q)
    {
        $qb = $this->createQueryBuilder('t');
        return $qb
            ->where(
                $qb->expr()->like('t.tag', ':tag')
            )
            ->setMaxResults(5)
            ->setParameter('tag', strtolower($q) . '%')
            ->getQuery()
            ->getResult();
    }

}
