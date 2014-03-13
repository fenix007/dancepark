<?php
namespace DancePark\DancerBundle\Form\DataTransformer;

use DancePark\CommonBundle\Entity\Place;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\TransformationFailedException;
use Symfony\Component\Form\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\File\File;

class StringToFileTransformer implements DataTransformerInterface
{

    /**
     * {@inheritDoc}
     * @var $value Place
     */
    public function transform($value)
    {
        if ($path = $value->getAbsolutePath()) {
            $value->setPhoto(new File($path));
        }
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        return $value;
    }
}