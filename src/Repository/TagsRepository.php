<?php

namespace App\Repository;

use App\Entity\ToolTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ToolTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method ToolTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method ToolTag[]    findAll()
 * @method ToolTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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

    /**
     * @param ToolTag $tag
     * @throws ORMException
     */
    public function remove(ToolTag $tag): void
    {
        $em = $this->getEntityManager();
        $em->remove($tag);
        $em->flush();
    }

    /**
     * @param ToolTag $tag
     * @throws ORMException
     */
    public function save(ToolTag $tag): void
    {
        $em = $this->getEntityManager();
        $em->persist($tag);
        $em->flush();
    }
}
