<?php
namespace DancePark\OrganizationBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToLessonTransformer implements  DataTransformerInterface
{

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        $result = array();
        $i = 0;
        if (null != $value) {
            foreach ($value as $label => $data) {
                $item = array();
                if (!is_array($data)) {
                    $item['lesson'] = $label;
                    $item['price'] = $data;
                } else {
                    $item['lesson'] = $data['lesson'];
                    $item['price'] = $data['price'];
                }
                if (!empty($item['lesson']) || !empty($item['price'])) {
                    $result[$i] = $item;
                    $i++;
                }
            }
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        $result = array();
        foreach ($value as $data) {
            if (isset($data['lesson']) && isset($data['price']))  {
                $result[$data['lesson']] = $data['price'];
            }
        }
        return $value;
    }
}