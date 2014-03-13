<?php
namespace DancePark\CommonBundle\DataFixtures\ORM;

use DancePark\CommonBundle\Entity\Place;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadPlaceData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $addressGroupRepo = $manager->getRepository('CommonBundle:AddressGroup');
        $city = $addressGroupRepo->find(2);
        $groups = $addressGroupRepo->findAll();
        for ($i = 0; $i < 100; ++$i) {
            $place = new Place();
            $place->setName($faker->word());
            $place->setCityId($city);
            $place->setAddrGroup($groups[rand(0, count($groups) - 1)]);
            $place->setLongtitude(rand(37500000, 38000000) / 1000000);
            $place->setLatitude(rand(55500000, 56000000) / 1000000);
            $place->setDescriptionTogo($faker->sentence());
            $place->setAddress($faker->address);
            $place->setWebUrl($faker->url);
            $place->setPhone1($faker->phoneNumber);
            $place->setEmail($faker->email);
            $manager->persist($place);
            $this->setReference('place:' . $i, $place);
        }
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
       return 2;
    }
}