<?php

namespace Anh\TiedContentBundle\Filter;

use Anh\DoctrineResourceBundle\AbstractFilter;

class ChildFilter extends AbstractFilter
{
    public function getSortFields(array $parameters = array())
    {
        return array(
            'child.updatedAt' => 'updatedAt',
            'child.createdAt' => 'createdAt',
            'child.title' => 'title',
        );
    }

    public function getDefinition(array $parameters = array())
    {
        return array(
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
        );
    }
}