<?php
namespace DancePark\EventBundle\DataFixture\ORM;

use DancePark\EventBundle\Entity\EventTypeGroup;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadEventTypeGroup extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $names = array('Учиться', 'Себя показать и других посмотреть', 'Просто потанцевать');
        for($i = 0; $i < 3; $i++) {
            $evTypeGroup = new EventTypeGroup();
            $evTypeGroup->setName($names[$i]);
            $manager->persist($evTypeGroup);
            $this->setReference('eventTypeGroup:' . $i, $evTypeGroup);
        }
        $manager->flush();
    }
}