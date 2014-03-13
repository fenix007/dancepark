<?php
namespace DancePark\FrontBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use DancePark\FrontBundle\Component\EventManager\EventManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class FrontController extends Controller
{
    /**
     * @Route("/{path}.html", name="page_action")
     * @Template()
     */
    public function pageAction(Request $request, $path) {
        /** @var $em EntityManager */
        $em =$this->get('doctrine')->getManager();
        $pagesRepo = $em->getRepository('CommonBundle:Page');

        $page = $pagesRepo->findOneBy(array('path' => $path, 'active' => true));
        if ($page) {
            return array('page' => $page);
        } else {
            throw $this->createNotFoundException();
        }
    }
    /**
     * @Route("/filters/{hash}", name="front", defaults={"hash" = ""})
     * @Route("/", name="front")
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $eventManager = new EventManager(
            $this->get('doctrine')->getEntityManager(),
            $this->get('form.factory'),
            $this->get('services_bag.filters_container'),
            $request
        );

        /** @var $form Form */
        $form = $eventManager->getForm();

        /** @var $quickForm Form */
        $quickForm = $eventManager->getQuickForm();

        $isNext = false;
        if ((count($request->query->all()) > 0 && count($eventManager->getData()) > 0)) {
            $events = $eventManager->getEventsByRequest($isNext, $request);

            $session = $request->getSession();
            if ($session !== null) {
                $session->set('filter_data', $form->getData());
            }

        } else {
            $events = array();
        }

        $isNextPageToday = false;
        $todayEvents = $eventManager->getTodayEvents($isNextPageToday);


        // Generate filter help links
        list($dateLinks, $timeLinks, $priceLinks) = $this->generateLinks();

        $data = $form->getData();

        $form = $form->createView();

        $q = $quickForm->createView();
        $children = $q->vars['form']->getChildren();
        $children['q']->vars['id'] = 'quick_filter';

        $bounds = $eventManager->getStreetBounds();

        $recommended = $eventManager->getRecommendedEvents();

        return array(
            'form' => $form,
            'quick_form' => $q,
            'events' => $events,
            'events_today' => $todayEvents,
            'date_links' => $dateLinks,
            'time_links' => $timeLinks,
            'price_links' => $priceLinks,
            'is_next' => $isNext,
            'is_next_today' => $isNextPageToday,
            'bounds' => $bounds,
            'data' => $data,
            'recommended' => $recommended,
            'hash' => $eventManager->getHash(),
            'authorization_message' => $request->get('authorization', false),
        );
    }

    /**
     * @return array
     */
    protected function generateLinks()
    {
        // Date links
        $currentDate = new \DateTime();

        $tomorrow = new \DateTime();
        $tomorrow->modify('tomorrow');

        $weStart = new \DateTime();
        $weStart->modify('next Saturday');

        $weEnd = clone($weStart);
        $weEnd->modify('next Sunday');

        $dateLinks = array(
            'label.today' => array('from' => $currentDate, 'to' => $currentDate),
            'label.tomorrow' => array('from' => $tomorrow, 'to' => $tomorrow),
            'label.weekend' => array('from' => $weStart, 'to' => $weEnd),
        );

        // Generate links for time filter
        $morningStart = new \DateTime(date('Y-m-d 01:00:00'));
        $morningEnd = new \DateTime(date('Y-m-d 12:59:59'));
        $dayStart = new \DateTime(date('Y-m-d 11:00:00'));
        $dayEnd = new \DateTime(date('Y-m-d 17:59:59'));
        $eveningStart = new \DateTime(date('Y-m-d 16:00:00'));
        $eveningEnd = new \DateTime(date('Y-m-d 23:59:59'));

        $timeLinks = array(
            'label.morning' => array('from' => $morningStart, 'to' => $morningEnd),
            'label.day' => array('from' => $dayStart, 'to' => $dayEnd),
            'label.evening' => array('from' => $eveningStart, 'to' => $eveningEnd),
        );

        // Links for price filter
        $priceLinks = array(
            'label.free' => array('from' => 0, 'to' => 0),
            'label.less5000' => array('from' => 1, 'to' => 5000),
            'label.greater5000' => array('from' => 5000, 'to' => null)
        );

        return array($dateLinks, $timeLinks, $priceLinks);
    }
}