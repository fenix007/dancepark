<?php
namespace DancePark\DancerBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DancerToStringTransformer implements DataTransformerInterface
{

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (is_object($value)) {
            return $value->getEmail();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        return $value;
    }
}