<?php
namespace DancePark\DancerBundle\FOS\Model;

use DancePark\DancerBundle\Entity\Dancer;
use FOS\UserBundle\Doctrine\UserManager as BaseManager;

class UserManager extends BaseManager
{
    public function setSecretAnswer(Dancer $dancer)
    {
        if (0 !== strlen($plain = $dancer->getPlainSecretAnswer())) {
            $encoder = $this->getEncoder($dancer);
            $dancer->setSecretAnswer($encoder->encodePassword($plain, $dancer->getSalt()));
        }
    }
}