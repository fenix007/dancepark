<?php

namespace DancePark\EventBundle\Controller;

use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\EventBundle\EventListener\Event\EventEvents;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\EventBundle\Entity\EventType;
use DancePark\EventBundle\Form\EventTypeType;

/**
 * EventType controller.
 *
 * @Route("/event_type")
 */
class EventTypeController extends Controller
{

    /**
     * Lists all EventType entities.
     *
     * @Route("/", name="admin_event_type")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EventBundle:EventType')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new EventType entity.
     *
     * @Route("/", name="admin_event_type_create")
     * @Method("POST")
     * @Template("EventBundle:EventType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new EventType();
        $form = $this->createForm(new EventTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_type_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new EventType entity.
     *
     * @Route("/new", name="admin_event_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EventType();
        $form   = $this->createForm(new EventTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a EventType entity.
     *
     * @Route("/{id}", name="admin_event_type_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:EventType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing EventType entity.
     *
     * @Route("/{id}/edit", name="admin_event_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:EventType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventType entity.');
        }

        $editForm = $this->createForm(new EventTypeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing EventType entity.
     *
     * @Route("/{id}", name="admin_event_type_update")
     * @Method("PUT")
     * @Template("EventBundle:EventType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:EventType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EventTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_type_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a EventType entity.
     *
     * @Route("/{id}", name="admin_event_type_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EventBundle:EventType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EventType entity.');
            }

            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');

            $event = new EntityEvent();
            $event->setDispatcher($eventDispatcher);
            $event->setEntity($entity);
            $eventDispatcher->dispatch(EventEvents::EVENT_TYPE_PRE_REMOVE, $event);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_event_type'));
    }

    /**
     * Creates a form to delete a EventType entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
