<?php

namespace Anh\TiedContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Anh\ContentBundle\Entity\Paper;
use Anh\ContentBundle\Entity\Category;
use Anh\TiedContentBundle\Entity\PaperTie;

class PaperTieRepository extends EntityRepository
{
    public function findParentsInSectionDQL($section)
    {
        return $this->createQueryBuilder('tie')
            ->select('tie, child, category')
            ->innerJoin('tie.child', 'child')
            ->leftJoin('child.category', 'category')
            ->where('tie.parent is null')
            ->andWhere('child.section = :section')
            ->setParameter('section', $section)
            ->orderBy('child.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findPublishedParentsInSectionDQL($section)
    {
        return $this->createQueryBuilder('tie')
            ->select('tie, child, category')
            ->innerJoin('tie.child', 'child')
            ->leftJoin('child.category', 'category')
            ->where('tie.parent is null')
            ->andWhere('child.section = :section')
            ->setParameter('section', $section)
            ->andWhere('child.publishedSince <= current_timestamp()')
            ->orderBy('child.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findPublishedParentsInCategoryDQL(Category $category)
    {
        return $this->createQueryBuilder('tie')
            ->select('tie, child, category')
            ->innerJoin('tie.child', 'child')
            ->leftJoin('child.category', 'category')
            ->where('tie.parent is null')
            ->andWhere('child.category = :category')
            ->setParameter('category', $category)
            ->andWhere('child.publishedSince <= current_timestamp()')
            ->orderBy('child.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findChildrenDQL(Paper $parent)
    {
        return $this->createQueryBuilder('tie')
            ->select('tie, child, category')
            ->innerJoin('tie.child', 'child')
            ->leftJoin('child.category', 'category')
            ->where('tie.parent = :parent')
            ->setParameter('parent', $parent)
            ->orderBy('tie.id')
            ->getQuery()
        ;
    }

    public function findTieDQL(Paper $child)
    {
        return $this->createQueryBuilder('tie')
            ->select('tie, parent, child, category')
            ->innerJoin('tie.child', 'child')
            ->leftJoin('tie.parent', 'parent')
            ->leftJoin('child.category', 'category')
            ->where('tie.child = :child')
            ->setParameter('child', $child)
            ->getQuery()
        ;
    }

    public function findPrev(PaperTie $current)
    {
        return $this->createQueryBuilder('tie')
            ->select('tie, child, category')
            ->innerJoin('tie.child', 'child')
            ->leftJoin('child.category', 'category')
            ->where('tie.parent = :parent')
            ->setParameter('parent', $current->getParent())
            ->andWhere('tie.id < :current')
            ->setParameter('current', $current->getId())
            ->orderBy('tie.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findNext(PaperTie $current)
    {
        return $this->createQueryBuilder('tie')
            ->select('tie, child, category')
            ->innerJoin('tie.child', 'child')
            ->leftJoin('child.category', 'category')
            ->where('tie.parent = :parent')
            ->setParameter('parent', $current->getParent())
            ->andWhere('tie.id > :current')
            ->setParameter('current', $current->getId())
            ->orderBy('tie.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
