<?php
namespace DancePark\OrganizationBundle\Form\DataTransformer;

use DancePark\OrganizationBundle\Entity\Organization;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class StringToFileTransformer implements DataTransformerInterface
{

    /**
     * {@inheritDoc}
     * @var $value Organization
     */
    public function transform($value)
    {
        if ($path = $value->getAbsolutePath()) {
            if (file_exists($path)) {
                $value->setLogo(new File($path));
            } else {
                $value->setLogo(null);
            }

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