<?php

namespace Anh\TiedContentBundle\Controller;

use Anh\DoctrineResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Anh\ContentBundle\Entity\Paper;
use Anh\ContentBundle\Entity\Category;
use Anh\ContentBundle\Controller\InjectOptionsTrait;

class TieController extends ResourceController
{
    use InjectOptionsTrait;

    public function createAction(Request $request)
    {
        return $this->handleResponse(parent::createAction($request));
    }

    public function updateAction(Request $request)
    {
        return $this->handleResponse(parent::updateAction($request));
    }

    protected function handleResponse($response)
    {
        if (isset($response['redirect'])) {
            if ($response['data']['resource_form']->get('child')->get('save_and_preview')->isClicked()) {
                $response['redirect'] = $this->container->get('anh_content.url_generator')->resolveAndGenerate(
                    $response['data']['resource']
                );
            }
        }

        return $response;
    }

    public function createChildAction(Request $request, Paper $parent)
    {
        $this->injectOptions($request, array(
            'form_options' => array(
                'parent' => $parent,
            ),
        ));

        $response = $this->createAction($request);
        $response['data']['parent'] = $parent;

        return $this->handleResponse($response);
    }

    public function updateChildAction(Request $request)
    {
        return $this->updateAction($request);
    }

    public function listChildrenAction(Request $request, Paper $parent)
    {
        $this->injectOptions($request, array(
            'data' => array(
                'parent' => $parent,
            ),
        ));

        return $this->listAction($request);
    }

    public function listParentsAction(Request $request, $section, $page = null, $limit = null)
    {
        $options = array(
            'view' => 'AnhTiedContentBundle:Default:_listParents.html.twig',
            'criteria' => array(
                'parent' => null,
                'child.section' => $section,
                '[isPublished]',
            ),
            'data' => array(
                'section' => $section,
            ),
        );

        if (!is_null($page)) {
            $options['page'] = $page;
            $options['view'] = 'AnhTiedContentBundle:Default:_paginateParents.html.twig';
        }

        if (!is_null($limit)) {
            $options['limit'] = $limit;
        }

        $this->injectOptions($request, $options);

        return $this->listAction($request);
    }

    public function viewParentAction(Request $request, $section, Paper $parent)
    {
        $repository = $this->container->get('anh_tied_content.tie.repository');

        $children = $repository
            ->fetch(array(
                'parent' => $parent,
            ))
        ;

        $this->injectOptions($request, array(
            'criteria' => array(
                'child' => $parent,
            ),
            'view' => 'AnhTiedContentBundle:Default:_viewParent.html.twig',
            'data' => array(
                'section' => $section,
                'parent' => $parent,
                'children' => $children,
            ),
        ));

        return $this->showAction($request);
    }

    public function viewChildAction(Request $request, $section, $child_slug)
    {
        $this->injectOptions($request, array(
            'method' => 'fetchOne',
            'criteria' => array(
                'child.section' => $section,
                'child.slug' => $child_slug,
            ),
            'view' => 'AnhTiedContentBundle:Default:_viewChild.html.twig',
            'data' => array(
                'section' => $section,
            ),
        ));

        $result = $this->showAction($request);

        $repository = $this->container->get('anh_tied_content.tie.repository');
        $current = $result['data']['resource'];

        $result['data']['navigation'] = array(
            'current' => $current,
            'prev' => $repository->findPrevTie($current),
            'next' => $repository->findNextTie($current),
        );

        return $result;
    }

    public function viewCategoryAction(Request $request, $section, Category $category, $page = null, $limit = null)
    {
        $options = array(
            'view' => 'AnhTiedContentBundle:Default:_viewCategory.html.twig',
            'criteria' => array(
                'parent' => null,
                'child.category' => $category,
                'child.section' => $section,
                '[isPublished]',
            ),
            'data' => array(
                'section' => $section,
                'category' => $category,
            ),
        );

        if (!is_null($page)) {
            $options['page'] = $page;
            $options['view'] = 'AnhTiedContentBundle:Default:_paginateCategory.html.twig';
        }

        if (!is_null($limit)) {
            $options['limit'] = $limit;
        }

        $this->injectOptions($request, $options);

        return $this->listAction($request);
    }
}
