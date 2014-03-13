<?php
namespace DancePark\OrganizationBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LessonToArrayTransformer implements DataTransformerInterface
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
            if ($value['lesson'] || $value['price']) {
                $result[$i] = $value;
                ++$i;
            }
        }
        return $result;
    }
}