<?php

namespace Anh\TiedContentBundle\Filter;

use Anh\DoctrineResourceBundle\AbstractFilter;
use Doctrine\ORM\EntityRepository;

class ParentFilter extends AbstractFilter
{
    protected $categoryClass;

    protected $sections;

    public function __construct($categoryClass, $sections)
    {
        $this->categoryClass = $categoryClass;
        $this->sections = $sections;
    }

    public function getSortFields(array $parameters = array())
    {
        if (!isset($parameters['section'])) {
            throw new \Exception("Parameter 'section' is required for filter.");
        }

        $section = $parameters['section'];

        $fields = array();

        if ($this->sections[$section]['publishedSince']) {
            $fields['child.publishedSince'] = 'publishedSince';
        }

        // if ($this->sections[$section]['category']) {
        //     $fields['child.category.title'] = 'category';
        // }

        return $fields + array(
            'child.updatedAt' => 'updatedAt',
            'child.createdAt' => 'createdAt',
            'child.title' => 'title',
        );
    }

    public function getDefinition(array $parameters = array())
    {
        if (!isset($parameters['section'])) {
            throw new \Exception("Parameter 'section' is required for filter.");
        }

        $section = $parameters['section'];

        $filter = array();

        if ($this->sections[$section]['category']) {
            $filter['category'] = array(
                'type' => 'entity',
                'field' => 'child.category',
                'form' => array(
                    'class' => $this->categoryClass,
                    'property' => 'title',
                    'empty_value' => 'All',
                    'query_builder' => function(EntityRepository $repository) use ($section) {
                        return $repository->prepareQueryBuilder([ 'section' => $section ], [ 'title' => 'asc']);
                    }
                ),
            );
        }

        return $filter + array(
            'title' => array(
                'type' => 'text',
                'field' => 'child.title',
                'operator' => function($value) {
                    if (strpos($value, '%') === false) {
                        $value = sprintf('%%%s%%', $value);
                    }

                    return [ '%child.title' => array('like' => $value) ];
                },
            ),

            'isDraft' => array(
                'type' => 'checkbox',
                'field' => 'child.isDraft',
                'empty_data' => false,
            ),
        );
    }
}
