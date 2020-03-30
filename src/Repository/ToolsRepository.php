<?php

namespace App\Repository;

use App\Entity\Tool;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ToolsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tool::class);
    }

    /**
     * @param string[] $tags
     * @return Tool[]
     */
    public function findByTags(array $tags): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.tags', 'tg')
            ->where('tg.tag IN (:tags)')
            ->setParameter('tags', $tags)
            ->getQuery()
            ->getResult();
    }

    public function searchTools($q)
    {
        $qb = $this->createQueryBuilder('t');
        return $qb
            ->where($qb->expr()->orX(
                $qb->expr()->like('t.name', ':query'),
                $qb->expr()->like('t.model', ':query'),
                $qb->expr()->like('t.code', ':query')
            ))
            ->setParameter('query', '%' . strtolower($q) . '%')
            ->getQuery()
            ->getResult();
    }

}
