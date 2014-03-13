<?php
namespace DancePark\FrontBundle\Controller;

use DancePark\CommonBundle\Entity\AddressGroup;
use DancePark\CommonBundle\Entity\AddressGroupRepository;
use DancePark\CommonBundle\Entity\DateRegularAbstract;
use DancePark\CommonBundle\Entity\MetroStation;
use DancePark\CommonBundle\Entity\MetroStationRepository;
use DancePark\CommonBundle\Entity\SearchKeywordsRepository;
use DancePark\DancerBundle\Entity\Dancer;
use DancePark\DancerBundle\Entity\DancerEvent;
use DancePark\DancerBundle\Entity\DancerRepository;
use DancePark\DancerBundle\EventListener\Form\DancerEventSubscriber;
use DancePark\EventBundle\Entity\Event;
use DancePark\EventBundle\Entity\EventRepository;
use DancePark\FrontBundle\Component\EventManager\EventManager;
use DancePark\FrontBundle\EventListener\Form\DancerProfileSubscriber;
use DancePark\FrontBundle\EventListener\Form\FeedbackEventSubscriber;
use DancePark\FrontBundle\Form\Type\DancerProfileType;
use DancePark\FrontBundle\Form\Type\FeedbackType;
use Doctrine\ORM\EntityManager;
use PhpOption\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     * Get json for quick search autocomplete
     *
     * @Route("/front/quickautocomplete", name="quick_filter_autocomplete")
     */
    public function quickAutocompleteAction(Request $request)
    {
        $value = $request->get('term');

        $pieces = explode(' ', $value);

        $value = array_pop($pieces);
        $prefix = implode(' ', $pieces);

        /** @var $keywordsRepository SearchKeywordsRepository */
        $keywordsRepository  = $this
            ->get('doctrine')
            ->getEntityManager()
            ->getRepository('CommonBundle:SearchKeywords');
        $keywords = $keywordsRepository->findByNameLikeBut($value, $pieces);
        $result = array();

        foreach ($keywords as $key) {
            if ($prefix) {
                $result[] = $prefix . ' ' . $key->getName();
            } else {
                $result[] = $key->getName();
            }
        }

        return new JsonResponse($result);
    }

    /**
     * Get json for quick search autocomplete
     *
     * @Route("/admin/dancer/email_autocomplete", name="dancer_email_api")
     */
    public function userEmailAutocompleteAction(Request $request)
    {
        $value = $request->get('term');

        /** @var DancerRepository $dancerRepo */
        $dancerRepo = $this->get('doctrine')->getManager()->getRepository('DancerBundle:Dancer');

        $dancers = $dancerRepo->findByDancerEmailLike($value);

        $resultNames = array();

        foreach ($dancers as $dancer) {
            $resultNames[$dancer->getId()] = $dancer->getEmail();
        }

        return new JsonResponse($resultNames);
    }

    /**
     * Get json for AddressGroup autocomplete
     *
     * @Route("/admin/address_group/street/autocomplete", name="address_street_autocomplete")
     */
    public function addressStreetAutocompleteAction(Request $request)
    {
        $value = $request->get('term');

        /** @var $addressGroupRepo AddressGroupRepository */
        $addressGroupRepo = $this->get('doctrine')->getManager()->getRepository('CommonBundle:AddressGroup');

        $streets = $addressGroupRepo->findGroupByNameLike($value);

        $result = array();

        foreach ($streets as $street) {
            /** @var $street AddressGroup */
            $result[$street->getId()] = $street->getName() . ' ' . $street->getPrefix();
        }

        return new JsonResponse($result);
    }

    /**
     * Get json for AddressGroup autocomplete
     *
     * @Route("/admin/address_group/street/check_autocomplete", name="address_street_check_autocomplete")
     */
    public function addressStreetCheckAutocompleteAction(Request $request)
    {
        $value = $request->get('term');

        /** @var $addressGroupRepo AddressGroupRepository */
        $addressGroupRepo = $this->get('doctrine')->getManager()->getRepository('CommonBundle:AddressGroup');

        $streets = $addressGroupRepo->findBy(array('name' => $value));

        $result = array();

        foreach ($streets as $street) {
            /** @var $street AddressGroup */
            $result[$street->getId()] = $street->getName() . ' ' . $street->getPrefix();
        }

        return new JsonResponse($result);
    }

    /**
     * Get json for AddressGroup autocomplete
     *
     * @Route("/admin/address_group/metro/autocomplete", name="metro_autocomplete")
     */
    public function metroAutocompleteAction(Request $request)
    {
        $value = $request->get('term');

        /** @var $metroRepo MetroStationRepository */
        $metroRepo = $this->get('doctrine')->getManager()->getRepository('CommonBundle:MetroStation');

        $stations = $metroRepo->findByNameLike($value);

        $result = array();

        foreach ($stations as $metro) {
            /** @var $metro MetroStation */
            $result[$metro->getId()] = $metro->getName();
        }

        return new JsonResponse($result);
    }

    /**
     * Get json for AddressGroup autocomplete
     *
     * @Route("/admin/address_group/metro/check_autocomplete", name="metro_check_autocomplete")
     */
    public function metroCheckAutocompleteAction(Request $request)
    {
        $value = $request->get('term');

        /** @var $metroRepo MetroStationRepository */
        $metroRepo = $this->get('doctrine')->getManager()->getRepository('CommonBundle:MetroStation');

        $stations = $metroRepo->findBy(array('name' => $value));

        $result = array();

        foreach ($stations as $metro) {
            /** @var $metro MetroStation */
            $result[$metro->getId()] = $metro->getName();
        }

        return new JsonResponse($result);
    }

    /**
     * Get next page
     *
     * @Route("/front/get_page/{page}", name="get_next_page")
     * @Template("FrontBundle:Api:result_list.html.twig")
     */
    public function getNextPageValuesAction(Request $request, $page)
    {
        $eventManager = new EventManager(
            $this->get('doctrine')->getEntityManager(),
            $this->get('form.factory'),
            $this->get('services_bag.filters_container'),
            $request
        );

        $isNext = false;
        $events = $eventManager->getEventsByRequest($isNext, $request, $page);

        return array(
            'events' => $events,
            'is_next' => $isNext,
            'hash' => $request->get('hash', '')
        );
    }

    /**
     * Get get reloaded page
     *
     * @Route("/front/refreshed_page/{page}", name="get_refreshed_page")
     * @Template("FrontBundle:Api:result_block.html.twig")
     */
    public function getDataListContainer(Request $request)
    {
        $eventManager = new EventManager(
            $this->get('doctrine')->getEntityManager(),
            $this->get('form.factory'),
            $this->get('services_bag.filters_container'),
            $request
        );

        $isNext = false;
        $events = $eventManager->getEventsByRequest($isNext, $request);
        $bounds = $eventManager->getStreetBounds();

        return array(
            'events' => $events,
            'is_next' => $isNext,
            'bounds' => $bounds,
            'hash' => $request->get('hash', '')
        );
    }

    /**
     * Get next page
     *
     * @Route("/front/get_page_today/{page}", name="get_next_page_today")
     * @Template("FrontBundle:Api:result_list.html.twig")
     */
    public function getNextPageTodayAction(Request $request, $page)
    {
        $session = $request->getSession();
        if ($data = $session->get('filter_data')) {
            $eventManager = new EventManager(
                $this->get('doctrine')->getEntityManager(),
                $this->get('form.factory'),
                $this->get('services_bag.filters_container'),
                $request
            );

            $isNext = false;
            $events = $eventManager->getTodayEvents($isNext, $page);

            return array(
                'events' => $events,
                'is_next' => $isNext,
                'hash' => $request->get('hash', '')
            );
        }
        return array('events' => array(), 'is_next' => false, 'hash' => $request->get('hash', ''));
    }


    /**
     * @Route("/join-event/{eventId}", name="api_join_event")
     */
    public function apiJoinMeAction($eventId)
    {
        try{
            $dancer = $this->getUser();
            if (!$dancer) {
                throw new \RuntimeException('No user authorized');
            }
            /** @var $em EntityManager */
            $em = $this->get('doctrine')->getManager();
            /** @var $eventRepo EventRepository */
            $eventRepo = $em->getRepository('EventBundle:Event');
            /** @var $event Event */
            $event = $eventRepo->find($eventId);
            if (!$event) {
                throw new \RuntimeException('Cant load event with $id' . $eventId);
            }
            $userEvent = new DancerEvent();
            $userEvent->setDancer($dancer);
            $userEvent->setEvent($event);

            if ($date = $event->getDate()) {
                $userEvent->setDate($date);
            } else if($event->getDateRegular()) {
                $currentDate = new \DateTime();
                $cDay = $currentDate->format('w');
                $minDistance = 7;
                foreach($event->getDateRegular() as $dateRegular) {
                    /** @var $dateRegular DateRegularAbstract */
                    $div = $dateRegular->getDayOfWeek() - $cDay;
                    if ($div < 0) {
                        $div = $dateRegular->getDayOfWeek() + 7 - $cDay;
                    }
                    if ($div < $minDistance) {
                        $minDistance = $div;
                    }
                }
                $nextDate = $currentDate->add(new \DateInterval('P' . $minDistance . 'D'));
                $userEvent->setDate($nextDate);
            }
            $translator = $this->get('translator');
            $userEvent->setStatus($translator->trans(DancerEvent::DANCER_STATUS_WILL));
            $em->persist($userEvent);
            $em->flush();
            return new JsonResponse(array('success' => 1, 'error' => 0));
        } catch(\Exception $e){
            return new JsonResponse(array('error' => 1, 'success' => 0));
        }
    }

    /**
 * @Route("/unjoin-event-api/{eventId}", name="api_unjoin_event")
 */
    public function unjoinDancerEventAction(Request $request, $eventId)
    {
        try{
            $dancer = $this->getUser();
            if (!$dancer) {
                throw new \RuntimeException('No user authorized');
            }
            /** @var $em EntityManager */
            $em = $this->get('doctrine')->getManager();
            $dancerEventRepo = $em->getRepository('DancerBundle:DancerEvent');
            $dancerEvent = $dancerEventRepo->find($eventId);
            if (!$dancerEvent) {
                throw new \RuntimeException('No Dancer Event found with id ' . $eventId);
            }

            $em->remove($dancerEvent);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 0, 'success' => 1));
            }else {
                return new RedirectResponse($this->get('router')->getGenerator()->generate('dancer_events', array('id' => $dancer->getId())));
            }
        } catch(\Exception $e){
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 1, 'success' => 0));
            }else {
                return new RedirectResponse($this->get('router')->getGenerator()->generate('dancer_events', array('id' => $dancer->getId())));
            }
        }
    }


    /**
     * @Route("/unjoin-event/{eventId}", name="unjoin_event")
     */
    public function unjoinApiDancerEventAction(Request $request, $eventId)
    {
        try{
            $dancer = $this->getUser();
            if (!$dancer) {
                throw new \RuntimeException('No user authorized');
            }
            /** @var $em EntityManager */
            $em = $this->get('doctrine')->getManager();
            $dancerEventRepo = $em->getRepository('DancerBundle:DancerEvent');
            $dancerEvent = $dancerEventRepo->findOneBy(array('dancer' => $dancer, 'event' => $eventId));
            if (!$dancerEvent) {
                throw new \RuntimeException('No Dancer Event found with id ' . $eventId);
            }

            $em->remove($dancerEvent);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 0, 'success' => 1));
            }else {
                return new RedirectResponse($this->get('router')->getGenerator()->generate('dancer_events', array('id' => $dancer->getId())));
            }
        } catch(\Exception $e){
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 1, 'success' => 0));
            }else {
                return new RedirectResponse($this->get('router')->getGenerator()->generate('dancer_events', array('id' => $dancer->getId())));
            }
        }
    }


    /**
     * @Route("/front/save/feedback?{id}", name="api_save_feedback")
     * @ParamConverter("event", class="EventBundle:Event")
     * @Method("POST")
     */
    public function saveFeedbackAction(Request $request, Event $event)
    {
        $form = $this->createForm(new FeedbackType(new FeedbackEventSubscriber(
            $this->get('doctrine'),
            $this->getUser(),
            $event,
            'event'
        )));
        $form->bind($request);

        if ($form->isValid()) {
            $manager = $this->get('doctrine')->getManager();
            $manager->persist($form->getData());
            $manager->flush();
        }
        return new Response($this->renderView('FrontBundle:Api:feedback_form.html.twig', array(
            'feedback_form' => $form->createView(),
            'event' => $event
        )));
    }
}