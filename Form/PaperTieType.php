<?php

namespace Anh\TiedContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaperTieType extends AbstractType
{
    protected $tieClass;

    protected $transformer;

    public function __construct($tieClass, $transformer)
    {
        $this->tieClass = $tieClass;
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hiddenFields = array();

        if ($options['parent']) {
            $builder->add($builder
                ->create('parent', 'hidden', array(
                    'data' => $options['parent'],
                    'data_class' => null,
                ))
                ->addViewTransformer($this->transformer)
            );
        }

        if ($options['parent'] || $builder->getData()->getParent()) {
            $hiddenFields = array(
                'category',
                'publishedSince',
                'isDraft',
                'metaAuthor',
                'metaKeywords',
                'metaDescription',
            );
        }

        $builder->add('child', 'anh_content_form_type_paper', array(
            'section' => $options['section'],
            'hidden_fields' => $hiddenFields,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->tieClass,
            'cascade_validation' => true,
            'parent' => null,
        ));

        $resolver->setRequired(array(
            'section',
        ));
    }

    public function getName()
    {
        return 'anh_tied_content_form_type_tie';
    }
}
