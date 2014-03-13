<?php
namespace DancePark\FrontBundle\Controller;

use DancePark\DancerBundle\Entity\Feedback;
use DancePark\EventBundle\Entity\EventRepository;
use DancePark\FrontBundle\Form\Type\FeedbackType;
use DancePark\EventBundle\Entity\Event;
use DancePark\FrontBundle\EventListener\Form\FeedbackEventSubscriber;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class EventController
 * @package DancePark\FrontBundle\Component
 * @Route("/event")
 */
class EventController extends Controller
{
    const COUNT_EVENT_PER_PAGE = 10;

    /**
     * @Route("/recommended", name="event_recommended")
     * @Template("FrontBundle:Event:list_recommended.html.twig")
     */
    public function listRecommendedAction(Request $request)
    {
        /** @var $dancerRepo EventRepository */
        $dancerRepo = $this->get('doctrine')->getManager()->getRepository('EventBundle:Event');

        $page = $request->get('page', 0);
        $offset = $page * static::COUNT_EVENT_PER_PAGE;

        $isNext = false;
        $isPrev = (bool)$page;
        try {
            $list = $dancerRepo->findBy(array('recommended' => true), array('id' => 'DESC'), static::COUNT_EVENT_PER_PAGE + 1, $offset);
        } catch(NotFoundHttpException $e){
            $list = array();
        }
        if (count($list) > static::COUNT_EVENT_PER_PAGE) {
            $isNext = true;
        }

        return array(
            'list' => array_slice($list, 0, static::COUNT_EVENT_PER_PAGE),
            'is_next' => $isNext,
            'is_prev' => $isPrev,
            'page' => $page
        );
    }
    /**
     * @Route("/{id}/{hash}", name="event_view", defaults={"hash" = ""})
     * @ParamConverter("event", class="EventBundle:Event")
     * @Template()
     */
    public function viewAction(Request $request, Event $event)
    {
        $form = null;
        $joined = null;
        if ($this->getUser()) {
            $form = $this->createForm(new FeedbackType(new FeedbackEventSubscriber(
                $this->get('doctrine'),
                $this->getUser(),
                $event,
                'event'
            )));

            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    $manager = $this->get('doctrine')->getManager();
                    $manager->persist($form->getData());
                    $manager->flush();

                    $form = $this->createForm(new FeedbackType(new FeedbackEventSubscriber(
                        $this->get('doctrine'),
                        $this->getUser(),
                        $event,
                        'event'
                    )));
                }
            }
            $form = $form->createView();

            $joinRepo = $this->get('doctrine')->getManager()->getRepository('DancerBundle:DancerEvent');
            $joined = $joinRepo->findOneBy(array('dancer' => $this->getUser(), 'event' => $event));
        }

        $hash = $request->get('hash', '');

        return array(
            'event' => $event,
            'feedback_form' => $form,
            'user' => $this->getUser(),
            'hash' => $hash,
            'joined' => $joined
        );
    }

    /**
     * @Route("/", name="event_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        /** @var $dancerRepo EventRepository */
        $dancerRepo = $this->get('doctrine')->getManager()->getRepository('EventBundle:Event');

        $page = $request->get('page', 0);
        $offset = $page * static::COUNT_EVENT_PER_PAGE;

        $isNext = false;
        $isPrev = (bool)$page;
        try {
            $list = $dancerRepo->findBy(array(), array('id' => 'DESC'), static::COUNT_EVENT_PER_PAGE + 1, $offset);
        } catch(NotFoundHttpException $e){
            $list = array();
        }
        if (count($list) > static::COUNT_EVENT_PER_PAGE) {
            $isNext = true;
        }

        return array(
            'list' => array_slice($list, 0, static::COUNT_EVENT_PER_PAGE),
            'is_next' => $isNext,
            'is_prev' => $isPrev,
            'page' => $page
        );
    }
}