<?php
namespace DancePark\CommonBundle\EventListener;

use DancePark\CommonBundle\Entity\MetroStation;
use DancePark\CommonBundle\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;

class SetMetroStationEventSubscriber implements EventSubscriberInterface
{
    protected $doctrine;
    public function __construct(Registry $doctrine)
    {
         $this->doctrine = $doctrine;
    }

    /**
     * {@inheritDoc}
     */
    public static  function getSubscribedEvents()
    {
        return array(
            'place.change' => 'checkPlaceMetroStation'
        );
    }

    public function checkPlaceMetroStation(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var $data Place */
        $data = $form->getData();
//        if (count($data->getMetro()) > 0) {
//            return $data;
//        }
        $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?' .
            'radius=1000' .
            '&types=subway_station' .
            '&sensor=false' .
            '&key=AIzaSyA3tjcuOMOF5IjjZJSInUo8mbZ6FZ7UyIY' .
            '&language=RU' .
            '&location=' . $data->getLatitude() . ',' . $data->getLongtitude();
        $content = file_get_contents($url);
        $result = json_decode($content);

        if (isset($result->results) && !empty($result->results)) {

            $raiting = array();

            foreach ($result->results as $id => $val) {
                $raiting[$id] =
                    abs($val->geometry->location->lat - $data->getLatitude()) +
                    abs($val->geometry->location->lng - $data->getLongtitude());
            }
            asort($raiting);
            reset($raiting);

            $keys = array_keys($raiting);

            $em = $this->doctrine->getManager();
            $metroRepo = $em->getRepository('CommonBundle:MetroStation');
            $data->setMetro(new ArrayCollection());
            for ($i = 0; $i < (count($raiting) < 3 ? count($raiting) : 3); ++$i) {
                $nearest = $keys[$i];

                $entity = $metroRepo->findOneBy(array('googleId' => $result->results[$nearest]->id));
                if (!$entity){
                    $entity = new MetroStation();
                    $entity->setGoogleId($result->results[$nearest]->id);
                    $entity->setName($result->results[$nearest]->name);
                    $location = $result->results[$nearest]->geometry->location;
                    $entity->setLatitude($location->lat);
                    $entity->setLongtitude($location->lng);
                    $em->persist($entity);
                }
                $data->addMetro($entity);
            }
        }
    }
}