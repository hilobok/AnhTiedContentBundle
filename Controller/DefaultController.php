<?php

namespace Anh\TiedContentBundle\Controller;

use Anh\TiedContentBundle\Controller\BaseController;
use Anh\ContentBundle\Entity\Category;

class DefaultController extends BaseController
{
    public function listParentsAction($section)
    {
        return $this->render('AnhTiedContentBundle:Default:_listParents.html.twig', array(
            'section' => $section,
            'parents' => $this->getPublishedParents($section)
        ));
    }

    public function paginateParentsAction($section, $page = 1, $limit = 10)
    {
        $pager = $this->paginatePublishedParents($section, $page, $limit)
            ->setUrl(str_replace('0', '{page}',
                $this->contentUrl('papers', $section, array(
                    'page' => 0
                ))
            ))
        ;

        return $this->render('AnhTiedContentBundle:Default:_paginateParents.html.twig', array(
            'section' => $section,
            'pager' => $pager
        ));
    }

    public function viewParentAction($section, $slug)
    {
        $parent = $this->getPaper($section, $slug);
        $children = $this->getChildren($parent);

        return $this->render('AnhTiedContentBundle:Default:_viewParent.html.twig', array(
            'section' => $section,
            'parent' => $parent,
            'children' => $children
        ));
    }

    public function viewChildAction($section, $child_slug)
    {
        $child = $this->getPaper($section, $child_slug);
        $navigation = $this->getNavigation($child);
        $tie = $this->getTie($child);

        return $this->render('AnhTiedContentBundle:Default:_viewChild.html.twig', array(
            'section' => $section,
            'child' => $child,
            'parent' => $tie->getParent(),
            'navigation' => $navigation
        ));
    }

    public function viewCategoryAction($section, $slug)
    {
        $category = $this->getCategory($section, $slug);
        $parents = $this->getPublishedParentsInCategory($category);

        return $this->render('AnhTiedContentBundle:Default:_viewCategory.html.twig', array(
            'section' => $section,
            'category' => $category,
            'parents' => $parents
        ));
    }

    public function paginateCategoryAction($section, $slug, $page = 1, $limit = 10)
    {
        $category = $this->getCategory($section, $slug);
        $pager = $this->paginatePublishedParentsInCategory($category, $page, $limit)
            ->setUrl(str_replace('0', '{page}',
                $this->contentUrl('category', $section, $category->getUrlParameters() + array(
                    'page' => 0
                ))
            ))
        ;

        return $this->render('AnhTiedContentBundle:Default:_paginateCategory.html.twig', array(
            'section' => $section,
            'category' => $category,
            'pager' => $pager
        ));
    }
}