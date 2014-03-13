<?php
namespace DancePark\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToGetPathTransformer implements DataTransformerInterface
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
                if (is_array($data)) {
                    $item['path'] = $data['path'];
                    $item['text'] = $data['text'];
                } else {
                    $item['path'] = $label;
                    $item['text'] = $data;
                }
                if ($item['path'] || $item['text']) {
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
            if (isset($data['path']) && isset($data['text']) && ($data['text'] || $data['path']))  {
                $result[$data['path']] = $data['text'];
            }
        }
        return $value;
    }
}