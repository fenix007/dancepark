<?php
namespace DancePark\FrontBundle\Controller;


use DancePark\DancerBundle\Entity\Dancer;
use DancePark\DancerBundle\Entity\Digest;
use DancePark\DancerBundle\Entity\SecretQuestion;
use DancePark\DancerBundle\EventListener\Form\DancerEventSubscriber;
use DancePark\EventBundle\Entity\EventRepository;
use DancePark\FrontBundle\Component\DigestManager;
use DancePark\FrontBundle\EventListener\Form\DancerProfileSubscriber;
use DancePark\FrontBundle\Form\Type\DancerChooseDanceType;
use DancePark\FrontBundle\Form\Type\DancerProfileType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ProfileController
 * @package DancePark\DancerBundle\Controller
 * @Route("/dancer")
 */
class ProfileController extends Controller
{
    const COUNT_DANCER_PER_PAGE = 10;


    /**
     * @Route("/list", name="dancers_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        /** @var $dancerRepo EventRepository */
        $dancerRepo = $this->get('doctrine')->getManager()->getRepository('DancerBundle:Dancer');

        $page = $request->get('page', 0);
        $offset = $page * static::COUNT_DANCER_PER_PAGE;

        $isNext = false;
        $isPrev = (bool)$page;
        try {
            $list = $dancerRepo->findBy(array(), array('id' => 'DESC'), static::COUNT_DANCER_PER_PAGE + 1, $offset);
        } catch(NotFoundHttpException $e){
            $list = array();
        }
        if (count($list) > static::COUNT_DANCER_PER_PAGE) {
            $isNext = true;
        }

        return array(
            'list' => array_slice($list, 0, static::COUNT_DANCER_PER_PAGE),
            'is_next' => $isNext,
            'is_prev' => $isPrev,
            'page' => $page
        );
    }
    /**
     * @Route("/change-secret-question", name="dancer_change_secret_question")
     * @Template("FrontBundle:Profile:change_secret.html.twig")
     */
    public function changePasswordAction(Request $request) {
        if (!$dancer = $this->getUser()) {
            throw $this->createNotFoundException($this->get('translator')->trans('label.page_not_found'));
        }
        /** @var $em EntityManager */
        $em = $this->get('doctrine')->getManager();
        $secretRepo = $em->getRepository('DancerBundle:SecretQuestion');

        $questions = $secretRepo->findAll();
        $choices = array();
        foreach ($questions  as $question) {
            /** @var $question SecretQuestion */
            $choices[$question->getId()] = $question->getQuestion();
        }

        $form = $this->createFormBuilder($dancer)
            ->add('secretQuestion', 'choice', array(
                'label' => 'label.secret_question',
                'choices' => $choices
            ))
            ->add('plainSecretAnswer', 'text', array(
                'label' => 'label.secret_answer'
            ))
            ->addEventSubscriber($this->get('front.dance.change_password'))
            ->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();

//                if ($plain = $data->getPlainSecretAnswer()) {
//                    $this->get('front.dance.change_password')->setSecretAnswer($data);
//                }

                $em->persist($data);
                $em->flush();
            }
        }

        return array(
            'dancer' => $dancer,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="dancer_view")
     * @Method("GET")
     * @ParamConverter("dancer", class="DancerBundle:Dancer")
     * @Template()
     */
    public function showAction(Dancer $dancer)
    {
        $currentDancer = false;
        if ($user = $this->getUser()) {
            if ($user->getId() == $dancer->getId()) {
                $currentDancer = true;
            }
        }
        return array('dancer' => $dancer, 'currentDancer' => $currentDancer);
    }

    /**
     * @Route("/{id}/edit", name="dancer_edit")
     * @ParamConverter("dancer", class="DancerBundle:Dancer")
     * @Template()
     */
    public function editAction(Request $request, Dancer $dancer)
    {
        if ($user = $this->getUser()) {
            if ($user->getId() != $dancer->getId() && !in_array(Dancer::ROLE_SUPER_ADMIN, $user->getRoles())) {
                throw $this->createNotFoundException();
            }
        } else {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new DancerProfileType(
                new DancerEventSubscriber($this->get('fos_user.user_manager')),
                new DancerProfileSubscriber($this->get('doctrine')->getManager()),
                $this->get('doctrine')->getManager()
            ),
            $dancer);
        $helperForm = $this->createForm(new DancerChooseDanceType($this->get('doctrine')->getManager()), $dancer);


        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();

                $em = $this->get('doctrine')->getManager();
                $em->persist($data);
                $em->flush();
            }
        }

        $digestManager = new DigestManager($this->get('doctrine')->getManager());

        $digestRepo = $this->get('doctrine')->getManager()->getRepository('DancerBundle:Digest');

        $digests = $digestRepo->findBy(array('dancer' => $dancer));
        $eventsResult = array();
        foreach ($digests as $digest) {
            $isNextPage = false;
            /** @var $digest Digest */
            $events = $digestManager->getAllEvents($digest, $isNextPage);
            foreach($events as $event) {
                $eventsResult[$event->getId()] = $event;
            }
        }

        return array(
            'my_events' => $eventsResult,
            'dancer' => $dancer,
            'form' => $form->createView(),
            'dance_type_form' => $helperForm->createView()
        );
    }

    /**
     * @Route("/{id}/events", name="dancer_events")
     * @ParamConverter("dancer", class="DancerBundle:Dancer")
     * @Template("FrontBundle:Profile:dancer_events.html.twig")
     */
    public function dancerEventsAction(Dancer $dancer)
    {
        if ($user = $this->getUser()) {
            if ($user->getId() != $dancer->getId() && !in_array(Dancer::ROLE_SUPER_ADMIN, $user->getRoles())) {
                throw $this->createNotFoundException();
            }
        } else {
            throw $this->createNotFoundException();
        }
        $dancerEventsRepo = $this->get('doctrine')->getManager()->getRepository('DancerBundle:DancerEvent');
        $dancerEvents = $dancerEventsRepo->findBy(array('dancer' => $dancer));
        return array(
            'dancer' => $dancer,
            'dancer_events' => $dancerEvents,
        );
    }
}