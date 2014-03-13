<?php
namespace DancePark\CommonBundle\DataFixtures\ORM;

use DancePark\CommonBundle\Entity\AddressLevel;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\Persistence\ObjectManager;
use Proxies\__CG__\DancePark\CommonBundle\Entity\AddressGroup;

class LoadAddressLevelData extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $city = new AddressLevel();
        $city->setName('city');
        $manager->persist($city);
        $street = new AddressLevel();
        $street->setName('street');
        $manager->persist($street);
        $manager->flush();
        $this->setReference('addressLevel:city', $city);
        $this->setReference('addressLevel:street', $street);
    }
}