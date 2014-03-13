<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use DancePark\CommonBundle\Entity\DanceType;
use DancePark\CommonBundle\Entity\DanceTypeRepository;
use DancePark\CommonBundle\Entity\PlaceRepository;
use DancePark\CommonBundle\Entity\SearchKeywords;
use DancePark\CommonBundle\Entity\SearchKeywordsRepository;
use DancePark\EventBundle\Entity\Event;
use DancePark\EventBundle\Entity\EventRepository;
use DancePark\EventBundle\Entity\EventTypeRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

class QuickFilter extends AbstractFilter
{
    protected $searchData = '';
    protected $secondaryData = array();

    /**
     * Validate Request parameter data? if exists
     *
     * @param $data mixed
     * @return bool
     */
    public function validateParameter($data)
    {
        if (isset($data['q']))
        {
            $this->searchData = $data['q'];
            return true;
        }
        return false;
    }

    /**
     * Provider filter action
     *
     * @param QueryBuilder $queryBuilder
     * @param mixed $data
     * @param Request $request
     *
     * @return mixed
     */
    public function applyFilter(QueryBuilder $queryBuilder, $data, Request $request = null)
    {

        $searchData = explode(' ', $this->searchData);
        $this->secondaryData = array_slice($searchData, 1);
        $searchData = $searchData[0];
        $page = $request->get('page', 0);


        /** @var $eventRepo EventRepository */
        $eventRepo = $this->em->getRepository('EventBundle:Event');

        /** @var $keysRepository SearchKeywordsRepository */
        $keysRepository = $this->em->getRepository('CommonBundle:SearchKeywords');
        /** @var $key SearchKeywords */
        $key = $keysRepository->findOneBy(array('name' => $searchData));
        $events = array();
        if ($key) {
            switch($key->getType()){
                case 1:
                    // Find by Event->name
                    $events = $eventRepo->findByNameLike($searchData, $this::PAGE_COUNT_RESULT, $page);$events = $eventRepo->findByNameLike($searchData, $this::PAGE_COUNT_RESULT, $page);
                    break;
                case 2:
                    // Find by Event->type
                    $events = $this->findByEventType($searchData, $eventRepo, $page);
                    break;
                case 3:
                    // Find by Event->danceType
                    $events = $this->findByDanceType($searchData, $eventRepo, $page);
                    break;
                case 4:
                    // Find by Event->infoColumn
                    $events = $eventRepo->findByInfoLike($searchData);
                    break;
                case 5:
                    // Find By Event->place
                    $events = $this->findEventsByPlace($searchData, $eventRepo, $page);
                    break;
            }
        } else {
            // Find by Event->name
            $events = $eventRepo->findByNameLike($searchData, $this::PAGE_COUNT_RESULT, $page);
            $eventKeys = array();
            if (empty($events)){
                // Find by Event->type
                $events = $this->findByEventType($searchData, $eventRepo, $page);

                if (empty($events)) {
                    // Find by Event->danceType
                    $events = $this->findByDanceType($searchData, $eventRepo, $page);

                    if (empty($events)) {
                        // Find by Event->infoColumn
                        $events = $eventRepo->findByInfoLike($searchData);

                        if (empty($events)) {
                            // Find By Event->place
                            $events = $this->findEventsByPlace($searchData, $eventRepo, $page);
                        }
                    }
                }
            }
        }
        $eventKeys = $this->getKeys($events);

        if (!empty($eventKeys)) {
            $queryBuilder->where('e.id IN (' . implode(', ', $eventKeys) . ')');
        } else {
            $queryBuilder->where('e.id = -1');
        }
    }

    /**
     * Check is $key on result
     *
     * @param $result Event
     * @param $key
     */
    protected function checkSearch($result, $key) {
        if (strpos($result->getName(), $key) !== false) {
            return true;
        }
        if (strpos($result->getType()->getName(), $key) !== false) {
            return true;
        }
        foreach ($result->getDanceType() as $res) {
            /** @var $res DanceType */
            if (strpos($res->getName(), $key) !== false) {
                return true;
            }
        }
        if (strpos($result->getInfoColumn(), $key) !== false) {
            return true;
        }
        if (strpos($result->getPlace()->getName(), $key) !== false) {
            return true;
        }
        return false;
    }

    /**
     * Provide functions impossible in SQL
     *
     * @return mixed
     */
    public function postExecuteFilter(array &$results, $filterData)
    {
        foreach ($this->secondaryData as $data) {
            $endResults = $results;
            foreach ($endResults as $id => $res) {
                if (!$this->checkSearch($res, $data)) {
                    unset($endResults[$id]);
                }
            }
            if (count($endResults) > 0) {
                $results = $endResults;
            }
        }
        return null;
    }

    /**
     * Get keys from entity array
     *
     * @param array $entities
     * @return array
     */
    public function getKeys(array $entities)
    {
         $keys = array();

        foreach ($entities as $entity) {
            $keys[] = $entity->getId();
        }

        return $keys;
    }

    /**
     * Add filter widget to the form while form building
     *
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return Form
     */
    public function editFilterForm(FormBuilder $formBuilder, Request $request = null)
    {
        $formBuilder
            ->add('q', 'genemu_jqueryautocomplete_text', array(
                'label' => 'label.quick_filter',
                'required' => false,
                'configs' => array('multiple' => true),
                'route_name' => 'quick_filter_autocomplete'
            ));
    }

    /**
     * @param $searchData
     * @param $eventRepo
     * @param $page
     * @return mixed
     */
    public function findEventsByPlace($searchData, $eventRepo, $page)
    {
        $events = array();
        /** @var $placeRepo PlaceRepository */
        $placeRepo = $this->em->getRepository('CommonBundle:Place');
        $places = $placeRepo->findByNameLike($searchData);

        if (count($places) > 0) {
            $events = $eventRepo->findBy(array('place' => $places), null, $this::PAGE_COUNT_RESULT, $this::PAGE_COUNT_RESULT * $page);
            return $events;
        }
        return $events;
    }

    /**
     * @param $searchData
     * @param $eventRepo
     * @param $page
     * @return mixed
     */
    public function findByDanceType($searchData, $eventRepo, $page)
    {
        $events = array();
        /** @var $danceTypeRepo DanceTypeRepository */
        $danceTypeRepo = $this->em->getRepository('CommonBundle:DanceType');
        $types = $danceTypeRepo->findByNameLike($searchData);

        if (count($types) > 0) {
            $events = $eventRepo->findByDanceTypes($this->getKeys($types), $this::PAGE_COUNT_RESULT, $page);
            return $events;
        }
        return $events;
    }

    /**
     * @param $searchData
     * @param $eventRepo
     * @param $page
     * @return mixed
     */
    public function findByEventType($searchData, $eventRepo, $page)
    {
        $events = array();
        /** @var $eventTypeRepo EventTypeRepository */
        $eventTypeRepo = $this->em->getRepository('EventBundle:EventType');
        $types = $eventTypeRepo->findByNameLike($searchData);

        if (count($types) > 0) {
            $events = $eventRepo->findBy(array('type' => $this->getKeys($types)), null, $this::PAGE_COUNT_RESULT, $this::PAGE_COUNT_RESULT * $page);
            return $events;
        }
        return $events;
    }
}