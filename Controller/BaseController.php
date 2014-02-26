<?php

namespace Anh\TiedContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anh\ContentBundle\Entity\Category;

class BaseController extends Controller
{
    /**
     * Generates url for content
     *
     * @param string $alias Route alias defined in section config
     * @param string $section Section name
     * @param array $parameters Additional route parameters
     */
    protected function contentUrl($alias, $section, array $parameters = array())
    {
        return $this->container->get('anh_content.url_generator')
            ->generateUrl($alias, $section, $parameters)
        ;
    }

    /**
     * Returns paper with given slug
     *
     * @param string $section
     * @param string $slug
     *
     * @return \Anh\ContentBundle\Entity\Paper
     */
    protected function getPaper($section, $slug)
    {
        $paper = $this->container->get('anh_content.manager.paper')
            ->findInSectionBySlug($section, $slug)
        ;

        if ($paper === null) {
            $this->createNotFoundException(
                sprintf("Unable to find paper in section '%s' with slug '%s'.", $section, $slug)
            );
        }

        return $paper;
    }

    /**
     * Returns category with given slug
     *
     * @param string $section Section name
     * @param string $slug
     *
     * @return \Anh\ContentBundle\Entity\Category
     */
    protected function getCategory($section, $slug)
    {
        $category = $this->container->get('anh_content.manager.category')
            ->findInSectionBySlug($section, $slug)
        ;

        if ($category === null) {
            $this->createNotFoundException(
                sprintf("Unable to find category in section '%s' with slug '%s'.", $section, $slug)
            );
        }

        return $category;
    }

    protected function getPublishedParents($section)
    {
        return $this->container->get('anh_tied_content.manager.tie')
            ->findPublishedParentsInSection($section)
        ;
    }

    protected function paginatePublishedParents($section, $page, $limit)
    {
        return $this->container->get('anh_tied_content.manager.tie')
            ->paginatePublishedParentsInSection($section, $page, $limit)
        ;
    }

    protected function getTie($child)
    {
        $tie = $this->container->get('anh_tied_content.manager.tie')
            ->getTie($child)
        ;

        return $tie;
    }

    protected function getChildren($parent)
    {
        $children = $this->container->get('anh_tied_content.manager.tie')
            ->findChildren($parent)
        ;

        return $children;
    }

    protected function getNavigation($child)
    {
        return $this->container->get('anh_tied_content.manager.tie')
            ->getNavigation($child)
        ;
    }

    /**
     * Returns categories list in section
     *
     * @param string $section Section name
     *
     * @return \Anh\ContentBundle\Entity\Category[]
     */
    protected function getCategories($section)
    {
        return $this->container->get('anh_content.manager.category')
            ->findInSection($section)
        ;
    }

    /**
     * Paginates categories list in section
     *
     * @param string $section Section name
     *
     * @return \Anh\PagerBundle\Pager
     */
    protected function paginateCategories($section, $page, $limit)
    {
        return $this->container->get('anh_content.manager.category')
            ->paginateInSection($section, $page, $limit)
        ;
    }

    protected function getPublishedParentsInCategory(Category $category)
    {
        return $this->container->get('anh_tied_content.manager.tie')
            ->findPublishedParentsInCategory($category)
        ;
    }

    protected function paginatePublishedParentsInCategory(Category $category, $page, $limit)
    {
        return $this->container->get('anh_tied_content.manager.tie')
            ->paginatePublishedParentsInCategory($category, $page, $limit)
        ;
    }
}