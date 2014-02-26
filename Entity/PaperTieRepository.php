<?php

namespace Anh\TiedContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Anh\ContentBundle\Entity\Paper;
use Anh\TiedContentBundle\Entity\PaperTie;

class PaperTieRepository extends EntityRepository
{
    public function findParentsInSectionDQL($section)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.child', 'c')
            ->where('t.parent is null')
            ->andWhere('c.section = :section')
            ->setParameter('section', $section)
            ->orderBy('c.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findPublishedParentsInSectionDQL($section)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.child', 'c')
            ->where('t.parent is null')
            ->andWhere('c.section = :section')
            ->setParameter('section', $section)
            ->andWhere('c.publishedSince <= current_timestamp()')
            ->orderBy('c.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findPublishedParentsInCategoryDQL($category)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.child', 'c')
            ->where('t.parent is null')
            ->andWhere('c.category = :category')
            ->setParameter('category', $category)
            ->andWhere('c.publishedSince <= current_timestamp()')
            ->orderBy('c.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findChildrenDQL(Paper $parent)
    {
        return $this->createQueryBuilder('t')
            // ->innerJoin('t.child', 'c')
            ->where('t.parent = :parent')
            ->setParameter('parent', $parent)
            ->orderBy('t.id')
            ->getQuery()
        ;
    }

    public function findPrev(PaperTie $current)
    {
        return $this->createQueryBuilder('t')
            ->where('t.parent = :parent')
            ->setParameter('parent', $current->getParent())
            ->andWhere('t.id < :current')
            ->setParameter('current', $current->getId())
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findNext(PaperTie $current)
    {
        return $this->createQueryBuilder('t')
            ->where('t.parent = :parent')
            ->setParameter('parent', $current->getParent())
            ->andWhere('t.id > :current')
            ->setParameter('current', $current->getId())
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
