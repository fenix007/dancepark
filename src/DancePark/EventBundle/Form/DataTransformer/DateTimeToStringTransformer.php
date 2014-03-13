<?php
namespace DancePark\EventBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class DateToStringTransformer
 * @package DancePark\EventBundle\Form\DataTransformmer]
 */
class DateTimeToStringTransformer implements DataTransformerInterface
{
    protected $format;

    /**
     * Construct object
     *
     * @param $format string
     */
    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * {@inheritDoc}
     * @var $value \DateTime
     */
    public function transform($value)
    {
        if (is_object($value) && $value instanceof \DateTime) {
            return $value->format($this->format);
        } else {
            return $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        return new \DateTime($value);
    }
}