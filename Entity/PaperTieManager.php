<?php

namespace Anh\TiedContentBundle\Entity;

use Anh\ContentBundle\AbstractModelManager;
use Doctrine\ORM\EntityManager;
use Anh\ContentBundle\Entity\Category;
use Anh\ContentBundle\Entity\Paper;
use Anh\TiedContentBundle\Entity\PaperTie;

class PaperTieManager extends AbstractModelManager
{
    public function __construct(EntityManager $em, $class, $pager)
    {
        parent::__construct($em, $class);
        $this->pager = $pager;
    }

    public function findParentsInSection($section)
    {
        $query = $this->repository
            ->findParentsInSectionDQL($section)
        ;

        return $query->getResult();
    }

    public function paginateParentsInSection($section, $page, $limit)
    {
        $query = $this->repository
            ->findParentsInSectionDQL($section)
        ;

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findPublishedParentsInSection($section)
    {
        $query = $this->repository
            ->findPublishedParentsInSectionDQL($section)
        ;

        return $query->getResult();
    }

    public function paginatePublishedParentsInSection($section, $page, $limit)
    {
        $query = $this->repository
            ->findPublishedParentsInSectionDQL($section)
        ;

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findPublishedParentsInCategory(Category $category)
    {
        $query = $this->repository
            ->findPublishedParentsInCategoryDQL($category)
        ;

        return $query->getResult();
    }

    public function paginatePublishedParentsInCategory(Category $category, $page, $limit)
    {
        $query = $this->repository
            ->findPublishedParentsInCategoryDQL($category)
        ;

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findChildren(Paper $parent)
    {
        $query = $this->repository
            ->findChildrenDQL($parent)
        ;

        return $query->getResult();
    }

    public function getTie(Paper $child)
    {
        $tie = $this->repository
            ->findOneBy(array('child' => $child))
        ;

        // search also in not yet flushed entities too (needed for url generation for new ties)
        if (empty($tie)) {
            $entities = $this->em->getUnitOfWork()->getScheduledEntityInsertions();
            foreach ($entities as $entity) {
                if (($entity instanceof PaperTie) && $entity->getChild() == $child) {
                    return $entity;
                }
            }
        }

        return $tie;
    }

    public function getPrev(PaperTie $tie)
    {
        return $this->repository->findPrev($tie);
    }

    public function getNext(PaperTie $tie)
    {
        return $this->repository->findNext($tie);
    }

    public function getNavigation(Paper $child)
    {
        $tie = $this->getTie($child);

        return array(
            'prev' => $this->getPrev($tie),
            'next' => $this->getNext($tie),
            'current' => $tie
        );
    }
}