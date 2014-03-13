<?php
namespace DancePark\CommonBundle\DataFixtures\ORM;

use DancePark\CommonBundle\Entity\DanceGroup;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadDanceGroupData extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i =0; $i < 10; ++$i) {
            $danceGroup  = new DanceGroup();
            $danceGroup->setName(implode(' ',$faker->words(rand(2, 5))));
            $manager->persist($danceGroup);
            $this->addReference('danceGroup:' . $i, $danceGroup);
        }
        $manager->flush();
    }
}