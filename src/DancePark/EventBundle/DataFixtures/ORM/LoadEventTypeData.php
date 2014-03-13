<?php
namespace DancePark\EventBundle\DataFixture\ORM;

use DancePark\EventBundle\Entity\EventType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadEventTypeData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $names = array(
            array('Регулярные группы', 'Мастер классы'),
            array('Фестивали', 'Конкурсы'),
            array('Вечеринки', 'Open Air')
        );
        $count = 0;
        for ($i = 0; $i < 3; ++$i) {
            for ($j = 0; $j < 2; ++$j) {
                $evType = new EventType();
                $evType->setName($names[$i][$j]);
                $evType->setTypeGroup($this->getReference('eventTypeGroup:' . $i));
                $manager->persist($evType);
                $this->setReference('eventType:' . $count, $evType);
                $count++;
            }
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