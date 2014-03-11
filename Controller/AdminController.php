<?php

namespace Anh\TiedContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Anh\TiedContentBundle\Entity\PaperTie;
use Anh\ContentBundle\Entity\Paper;

class AdminController extends Controller
{
    public function indexAction()
    {
        $sections = $this->container->getParameter('anh_tied_content.sections');

        return $this->render('AnhTiedContentBundle:Admin:index.html.twig', array(
            'sections' => $sections
        ));
    }

    // books
    public function listParentsAction($section, $page = 1, $limit = 10)
    {
        $sections = $this->container->getParameter('anh_tied_content.sections');

        $pager = $this->container->get('anh_tied_content.manager.tie')
            ->paginateParentsInSection($section, $page, $limit)
            ->setUrl(str_replace('0', '{page}',
                $this->generateUrl('anh_tied_content_admin_parent_list', array(
                    'section' => $section,
                    'page' => 0
                ))
            ))
        ;

        return $this->render('AnhTiedContentBundle:Admin:listParents.html.twig', array(
            'sections' => $sections,
            'section' => $section,
            'pager' => $pager
        ));
    }

    public function addParentAction($section)
    {
        // check if section available in book bundle

        $paper = $this->container->get('anh_content.manager.paper')->create();
        $paper->setSection($section);

        // create and persist without flush (needed for url generation)
        $tm = $this->container->get('anh_tied_content.manager.tie');
        $tie = $tm->create();
        $tie->setParent(null);
        $tie->setChild($paper);
        $tm->save($tie, false);

        $response = $this->paperAddEdit(
            $paper,
            'AnhTiedContentBundle:Admin:addParent.html.twig',
            $this->generateUrl('anh_tied_content_admin_parent_list', array('section' => $section))
        );

        if ($response->isRedirection()) {
            $errors = $this->container->get('validator')->validate($tie);
            if (count($errors) > 0) {
                throw new \InvalidArgumentException((string) $errors);
            }
            $tm->save($tie);
        }

        return $response;
    }

    public function editTieAction(PaperTie $tie)
    {
        $paper = $tie->getChild();

        $options = ($tie->getParent() === null) ? array() : array(
            'hidden_fields' => array(
                'category',
                'publishedSince',
                'isDraft',
                'metaAuthor',
                'metaKeywords',
                'metaDescription'
            )
        );

        $response = $this->paperAddEdit(
            $paper,
            ($tie->getParent() === null) ? 'AnhTiedContentBundle:Admin:editParent.html.twig' : 'AnhTiedContentBundle:Admin:editChild.html.twig',
            $this->getRequest()->server->get('HTTP_REFERER'),
            $options,
            array(
                'parent' => $tie->getParent()
            )
        );

        if ($response->isRedirection()) {
            $this->container->get('anh_content.manager.paper')->save($paper);
        }

        return $response;
    }

    // chapters
    public function listChildAction(Paper $parent)
    {
        $sections = $this->container->getParameter('anh_tied_content.sections');
        $children = $this->container->get('anh_tied_content.manager.tie')->findChildren($parent);

        return $this->render('AnhTiedContentBundle:Admin:listChildren.html.twig', array(
            'sections' => $sections,
            'children' => $children,
            'parent' => $parent
        ));
    }

    public function addChildAction(Paper $parent)
    {
        $child = $this->container->get('anh_content.manager.paper')->create();
        $child->setSection($parent->getSection());

        // create and persist without flush (needed for url generation)
        $tm = $this->container->get('anh_tied_content.manager.tie');
        $tie = $tm->create();
        $tie->setParent($parent);
        $tie->setChild($child);
        $tm->save($tie, false);

        $response = $this->paperAddEdit(
            $child,
            'AnhTiedContentBundle:Admin:addChild.html.twig',
            $this->generateUrl('anh_tied_content_admin_child_list', array('parent' => $parent->getId())),
            array(
                'hidden_fields' => array(
                    'category',
                    'publishedSince',
                    'isDraft',
                    'metaAuthor',
                    'metaKeywords',
                    'metaDescription'
                )
            ),
            array(
                'parent' => $parent
            )
        );

        if ($response->isRedirection()) {
            $errors = $this->container->get('validator')->validate($tie);
            if (count($errors) > 0) {
                throw new \InvalidArgumentException((string) $errors);
            }

            $tm->save($tie);
        }

        return $response;
    }

    protected function paperAddEdit(Paper $paper, $template, $redirect, $options = array(), $extra = array())
    {
        $form = $this->createForm('anh_content_form_type_paper', $paper, $options);
        $form->get('_redirect')->setData($redirect);

        $request = $this->getRequest();
        $section = $paper->getSection();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $this->container->get('anh_content.manager.paper')->save($paper, false);

                return $this->redirect($form->get('_redirect')->getData());
            }
        }

        $options = $this->container->getParameter('anh_content.options');
        $sections = $this->container->getParameter('anh_tied_content.sections');

        // getting all available bbcode tags from parser
        $parser = $this->container->get('anh_markup.parser');
        $tags = $parser->command('getTags', 'bbcode', '', array(
            'entity' => $paper
        ));

        return $this->render($template, array(
            'tags' => $tags,
            'sections' => $sections,
            'options' => $options,
            'section' => $section,
            'form' => $form->createView()
        ) + $extra);
    }

    public function deleteAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $list = $request->request->get('id');

            if (!empty($list) and is_array($list)) {
                $this->container->get('anh_content.manager.paper')->deleteByIdList($list);
            }
        }

        return $this->redirect($request->request->get('_redirect'));
    }
}