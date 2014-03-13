<?php
namespace DancePark\EventBundle\DataFixture\ORM;

use DancePark\EventBundle\Entity\DateRegularWeek;
use DancePark\EventBundle\Entity\Event;
use DancePark\EventBundle\Entity\EventLessonPrice;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadEventData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 100; ++$i) {
            $event = new Event();
            $event->setName(implode(' ', $faker->words(rand(1, 5))));
            $event->setAbonement(rand(0,1));
            $event->setChildren(rand(0, 1));
            $event->setRecommended(rand(0, 1));
            $event->addDanceType($this->getReference('danceType:' . rand(0, 9)));
            $event->addOrganization($this->getReference('org:' . rand(0, 9)));
            $event->setType($this->getReference('eventType:' . rand(0, 5)));
            $event->setInfoColumn(implode(' ', $faker->sentences(rand(1, 5))));
            $event->setDescription(implode(' ', $faker->sentences(rand(1, 3))));
            $event->setShortInfo($faker->sentence);
            $event->setCheckDate($faker->dateTime);
            $event->setStartTime($faker->dateTime);
            $event->setEndTime($faker->dateTime);
            $event->setPlace($this->getReference('place:' . rand(0, 99)));
            $kind = rand(1, 2);
            $event->setKind($kind);
            switch($kind) {
                case 2:
                    $event->setDate($faker->dateTime);
                    $event->setPrice(rand(100, 999999)/100);
                    break;
                case 1:
                    for ($j = 0; $j < rand(1, 5); ++$j) {
                        $dayOfWeek = new DateRegularWeek();
                        $dayOfWeek->setDayOfWeek(rand(1, 7));
                        $dayOfWeek->setStartTime($faker->dateTime);
                        $dayOfWeek->setEndTime($faker->dateTime);
                        $event->addDateRegular($dayOfWeek);
                        $dayOfWeek->setEvent($event);
                        $manager->persist($dayOfWeek);

                        $lesson = new EventLessonPrice();
                        $lesson->setEvent($event);
                        $lesson->setPrice(rand(100, 999999)/100);
                        $lesson->setLesson($j);
                        $manager->persist($lesson);
                    }
                    break;
            }
            $manager->persist($event);
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
        return 4;
    }
}