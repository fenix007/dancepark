<?php
namespace DancePark\DancerBundle\DataFixtures\ORM;

use DancePark\DancerBundle\Entity\Dancer;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadUserData extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; ++$i) {
            $dancer = new Dancer();
            $dancer->setFirstName($faker->firstName);
            $dancer->setLastName($faker->lastName);
            $dancer->setBirthday($faker->dateTimeThisCentury);
            $dancer->setUsername($faker->name);
            $dancer->setEmail($faker->email);
            $dancer->setPhone1($faker->phoneNumber);
            $dancer->setShortOverview($faker->sentence);
            $dancer->setPlainPassword($faker->word);
            $dancer->setLocked(false);
            $dancer->setEnabled(true);
            $dancer->setExpired(true);
            $dancer->setBiography($faker->text);
            $manager->persist($dancer);
        }
        $manager->flush();
    }
}