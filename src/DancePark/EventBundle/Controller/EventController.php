<?php

namespace DancePark\EventBundle\Controller;

use DancePark\CommonBundle\Controller\AbstractCRUDController;
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
class EventController extends AbstractCRUDController
{
    /**
     *
     */
    function getEntityName()
    {
        return 'EventBundle:Event';
    }
    /**
     * Creates a new Event entity.
     *
     * @Route("/", name="admin_event_create")
     * @Method("POST")
     * @Template("EventBundle:Event:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Event();
        $form = $this->createForm(
            new EventType(
                new EventEventSubscriber($this->get('doctrine')),
                $this->get('common.event.date_regular_subscriber')
            ), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Event entity.
     *
     * @Route("/new", name="admin_event_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Event();
        $form   = $this->createForm(
            new EventType(
                new EventEventSubscriber($this->get('doctrine')),
                $this->get('common.event.date_regular_subscriber')
            ), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/{id}/edit", name="admin_event_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $editForm = $this->createForm(
            new EventType(
                new EventEventSubscriber($this->get('doctrine')),
                $this->get('common.event.date_regular_subscriber')
            ), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Event entity.
     *
     * @Route("/{id}", name="admin_event_update")
     * @Method("PUT")
     * @Template("EventBundle:Event:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(
            new EventType(
                new EventEventSubscriber($this->get('doctrine')),
                $this->get('common.event.date_regular_subscriber')
            ), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}", name="admin_event_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EventBundle:Event')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Event entity.');
            }

            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch(EventEvents::EVENT_PRE_REMOVE, $entityEvent);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_event'));
    }
}
