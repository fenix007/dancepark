<?php
namespace DancePark\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Base admin page
 * @package DancePark\CommonBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * Base admin page
     *
     * @Route("/", name="admin_root")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        /**@var $router \Symfony\Bundle\FrameworkBundle\Routing\Router*/
        $router = $this->get('router');
        $menu =  array(
            // Event controllers
            'event' => array(
                'link' => $router->generate("admin_event"),
                'title' => 'Event'
            ),
            'event_closing' => array(
                'link' => $router->generate('admin_event_closing'),
                'title' => 'Event Closing'
            ),
            'event_type' => array(
                'link' => $router->generate('admin_event_type'),
                'title' => 'Event Type'
            ),
            'event_type_group' => array(
                'link' => $router->generate('admin_event_type_group'),
                'title' => 'Event Type Group'
            ),
            'event_lesson_price' => array(
                'link' => $router->generate('event_lesson_price'),
                'title' => 'Event Lesson Price'
            ),
            'date_regular_week' => array(
                'link' => $router->generate('admin_dete_regular_week'),
                'title' => 'Date Regular Week',
            ),

            // Organization controllers
            'organization' => array(
                'link' => $router->generate('admin_organization'),
                'title' => 'Organization',
            ),

            // Dancer controllers
            'dancer' => array(
                'link' => $router->generate('admin_dancer'),
                'title' => 'Dancer',
            ),
            'secret_question' => array(
                'link' => $router->generate('secret_question'),
                'title' => 'Secret Question'
            ),
            'dancer_event' => array(
                'link' => $router->generate('admin_dancer_event'),
                'title' => 'Dancer Event'
            ),
            'digest' => array(
                'link' => $router->generate('admin_digest'),
                'title' => 'Digest'
            ),
            'feedback' => array(
                'link' => $router->generate('admin_feedback'),
                'title' => 'Feedback'
            ),

            // Common controllers
            'article' => array(
                'link' => $router->generate('article'),
                'title' => 'Article'
            ),
            'address_group' => array(
                'link' => $router->generate('admin_address_group'),
                'title' => 'Address Group',
            ),
            'address_level' => array(
                'link' => $router->generate('admin_address_level'),
                'title' => 'Address level'
            ),
            'address_region' => array(
                'link' => $router->generate('admin_address_region'),
                'title' => 'Address Region'
            ),
            'dance_group' => array(
                'link' => $router->generate("admin_dance_group"),
                'title' => 'Dance group',
            ),
            'dance_type' => array(
                'link' => $router->generate('dance_type'),
                'title' => 'Dance type',
            ),
            'metro_station' => array(
                'link' => $router->generate('metro_station'),
                'title' => 'Metro station',
            ),
            'place' => array(
                'link' => $router->generate('place'),
                'title' => 'Place'
            ),
            'search_keywords' => array(
                'link' => $router->generate('search_keywords'),
                'title' => 'Search Keywords'
            ),
            'page' => array(
                'link' => $router->generate('page'),
                'title' => 'Page'
            ),
        );
        return array('menu' => $menu);
    }
}