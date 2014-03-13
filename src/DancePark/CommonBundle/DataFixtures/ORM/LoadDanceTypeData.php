<?php
namespace DancePark\CommonBundle\DataFixtures\ORM;

use DancePark\CommonBundle\Entity\DanceType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadDanceTypeData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $kinds = DanceType::getAvaliableKinds(true);
        for($i = 0; $i < 10; ++$i) {
            $danceType = new DanceType();
            $danceType->setName($faker->word());
            $danceType->setDanceGroup($this->getReference('danceGroup:' . rand(0, 9)));
            $danceType->setKind($kinds[rand(0, count($kinds) - 1)]);
            $this->setReference('danceType:' . $i, $danceType);
            $manager->persist($danceType);
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
        return 1;
    }
}