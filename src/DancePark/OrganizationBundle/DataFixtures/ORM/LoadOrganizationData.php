<?php
namespace DancePark\OrganizationBundle\DataFixtures\ORM;

use DancePark\OrganizationBundle\Entity\Organization;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadOrganizationData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for($i = 0; $i < 10; ++$i) {
             $organization = new Organization();

            $organization->setName(implode(' ', $faker->words(rand(1, 5))));
            $organization->setEmail($faker->email);
            $organization->setDateOfIncorporation($faker->dateTime);
            $organization->setPhone1($faker->phoneNumber);
            $organization->setShortDescription($faker->sentence);
            $organization->setType($faker->word);
            $organization->setWebUrl($faker->url);
            $pids = array();
            for ($j = 0; $j < rand(1, 3); ++$j) {
                $id = rand(0, 99);
                if (!in_array($id, $pids)) {
                    $organization->addPlace($this->getReference('place:' . $id));
                    $pids[] = $id;
                }
            }
            $manager->persist($organization);
            $this->setReference('org:' . $i, $organization);
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
        return 3;
    }
}