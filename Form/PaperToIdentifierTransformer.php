<?php

namespace Anh\TiedContentBundle\Form;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class PaperToIdentifierTransformer implements DataTransformerInterface
{
    protected $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        $paper = $this->repository->find($value);

        if (!$paper) {
            throw new TransformationFailedException(
                sprintf("Paper with identifier '%s' does not exist.", $value)
            );
        }

        return $paper;
    }

    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        $class = $this->repository->getClassName();

        if (!$value instanceof $class) {
            throw new UnexpectedTypeException($value, $class);
        }

        return $value->getId();
    }
}