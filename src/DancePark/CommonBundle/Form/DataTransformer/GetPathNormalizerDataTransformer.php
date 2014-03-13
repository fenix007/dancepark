<?php
namespace DancePark\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class GetPathNormalizerDataTransformer implements DataTransformerInterface
{

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        $result = array();
        $i = 0;
        foreach($value as $id => $value) {
            if ($value['text'] || $value['path']) {
                $result[$i] = $value;
                ++$i;
            }
        }
        return $result;
    }
}