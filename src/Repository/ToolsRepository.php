<?php

namespace App\Repository;

use App\Entity\Tool;
use App\Entity\ToolLog;
use App\Entity\ToolTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @method Tool|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tool|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tool[]    findAll()
 * @method Tool[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ToolsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tool::class);
    }

    public function paginate(string $searchParam = null): array
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT t 
            FROM App\Entity\Tool t
            WHERE 
            t.name LIKE :searchParam 
            OR t.model LIKE :searchParam 
            OR t.code LIKE :searchParam
            OR t.id IN (SELECT tool.id FROM App\Entity\Tool tool JOIN tool.tags tag WHERE tag.tag = :exactTag )'
        )->setParameter('searchParam', '%' . strtolower($searchParam) . '%')
            ->setParameter('exactTag', strtolower($searchParam));

        $results = $query->getArrayResult();
        foreach ($results as $key => $value) {
            $tool = $this->findOneBy(['code' => $value['code']]);
            $photo = $tool->getPhotos()[0];
            $results[$key]['tags'] = $tool->getTagsArray();
            if ($photo != null)
                $results[$key]['photo'] = $photo->getFileName();
        }
        return $results;
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
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('t.name', ':query'),
                    $qb->expr()->like('t.model', ':query'),
                    $qb->expr()->like('t.code', ':query')
                )
            )
            ->setParameter('query', '%' . strtolower($q) . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Tool $tool
     * @throws ORMException
     */
    public function remove(Tool $tool): void
    {
        $em = $this->getEntityManager();
        $tags = $tool->getTags() ?? [];
        foreach ($tags as $tag) {
            if ($tag->getTools()->count() <= 1) {
                $em->remove($tag);
            }
        }

        $em->remove($tool);
        $em->flush();
    }

    /**
     * @param Tool $tool
     * @throws ORMException
     */
    public function save(Tool $tool): void
    {
        $em = $this->getEntityManager();
        $this->setToolStatus($tool);
        $tags = $tool->getTags() ?? [];
        foreach ($tags as $key => $value) {
            $value->addTool($tool);
        }
        $em->persist($tool);
        $em->flush();
    }

    /**
     * @param Tool $tool
     * @throws ORMException
     */
    public function update(Tool $tool): void
    {
        $em = $this->getEntityManager();
        $originalData = $em->getUnitOfWork()->getOriginalEntityData($tool);
        $originalTags = [];
        if (isset($originalData['tags']) && $originalData['tags'] instanceof PersistentCollection) {
            $originalTags = $originalData['tags']->toArray();
        }

        //----------------- Tags ------------------
        $submittedTags = [];
        if ($tool->getTags()) {
            $submittedTags = $tool->getTags()->toArray();
        }

        $removedTags = $this->diffTagSets($originalTags, $submittedTags);
        $addedTags = $this->diffTagSets($submittedTags, $originalTags);

        foreach ($removedTags as $tag) {
            $tag->removeTool($tool);
            if ($tag->getTools()->count() === 0) {
                $em->remove($tag);
            }
        }

        foreach ($addedTags as $tag) {
            $tag->addTool($tool);
        }

        //----------------- Logs ------------------
        // reikia panaikinti tuščią įvedimo lauką jeigu forma buvo siųsta nieko nekeitus log'uose
        // TODO ^
        foreach ($tool->getLogs() as $log) {
            if (!$log->getLog()) {
                $tool->removeLog($log);
            }
        }

        //----------------- Params ----------------
        // TODO
        //----------------------------------------------
        $this->setToolStatus($tool);
        $em->persist($tool);
        $em->flush();
    }
    private function setToolStatus(Tool $tool)
    {
        if ($tool->getLogs()->last())
            $tool->setStatus($tool->getLogs()->last()->getType());
    }
    /**
     * @param Tool $tool
     * @param ToolLog $log 
     */
    public function addToolLog(Tool $tool, ToolLog $log): void
    {
        $em = $this->getEntityManager();
        $tool->addLog($log);
        $this->setToolStatus($tool);
        $em->persist($tool);
        $em->flush();
    }

    /**
     * @param ToolTag[] $set1
     * @param ToolTag[] $set2
     * @return ToolTag[]
     */
    private function diffTagSets(array $set1, array $set2): array
    {
        return array_udiff(
            $set1,
            $set2,
            static function (ToolTag $arr1, ToolTag $arr2) {
                return $arr1->getId() - $arr2->getId();
            }
        );
    }
}
