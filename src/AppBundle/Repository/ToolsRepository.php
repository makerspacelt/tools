<?php
/**
 * Created by PhpStorm.
 * User: Kulverstukas
 * Date: 2018-09-21
 * Time: 20:38
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ToolsRepository extends EntityRepository {

    public function searchTools($q) {
        $qb = $this->createQueryBuilder('t');
        return $qb->
            where($qb->expr()->orX(
                $qb->expr()->like('t.name', ':query'),
                $qb->expr()->like('t.model', ':query'),
                $qb->expr()->like('t.code', ':query')
            ))->
            setParameter('query', '%'.strtolower($q).'%')->
            getQuery()->getResult();
    }

}