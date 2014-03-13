<?php

namespace DancePark\CommonBundle\Controller;

use DancePark\FrontBundle\Component\EventManager\Filter\FilterInterface;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\EventBundle\Entity\DateRegularWeek;
use DancePark\EventBundle\EventListener\Event\EventEvents;
use DancePark\EventBundle\EventListener\Form\EventEventSubscriber;
use DancePark\EventBundle\Form\DateRegularWeekType;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\EventBundle\Entity\Event;
use DancePark\EventBundle\Form\EventType;

/**
 * Event controller.
 *
 * @Route("/event")
 */
abstract class AbstractCRUDController extends Controller
{
    abstract function getEntityName();

    /**
     * Lists all Event entities.
     *
     * @Route("/", name="admin_event")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $page = $this->getRequest()->get('page');

        //$entities = $em->getRepository($this->getEntityName())->findBy(array(), array(), FilterInterface::PAGE_COUNT_RESULT, FilterInterface::PAGE_COUNT_RESULT * $page);
        $entities = $em->getRepository($this->getEntityName())->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}", name="admin_event_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->getEntityName())->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to delete a Event entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
            ;
    }
}
