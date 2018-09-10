<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TagsRepository extends EntityRepository {

    public function searchTags($q) {
        $qb = $this->createQueryBuilder('t');
        return $qb
            ->where(
                $qb->expr()->like('t.tag', ':tag')
            )
            ->setMaxResults(5)
            ->setParameter('tag', $q.'%')
            ->getQuery()
            ->getResult();
    }

}