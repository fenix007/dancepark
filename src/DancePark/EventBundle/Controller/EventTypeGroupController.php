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
use DancePark\EventBundle\Entity\EventTypeGroup;
use DancePark\EventBundle\Form\EventTypeGroupType;

/**
 * EventTypeGroup controller.
 *
 * @Route("/event_type_group")
 */
class EventTypeGroupController extends Controller
{

    /**
     * Lists all EventTypeGroup entities.
     *
     * @Route("/", name="admin_event_type_group")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EventBundle:EventTypeGroup')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new EventTypeGroup entity.
     *
     * @Route("/", name="admin_event_type_group_create")
     * @Method("POST")
     * @Template("EventBundle:EventTypeGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new EventTypeGroup();
        $form = $this->createForm(new EventTypeGroupType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_type_group_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new EventTypeGroup entity.
     *
     * @Route("/new", name="admin_event_type_group_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EventTypeGroup();
        $form   = $this->createForm(new EventTypeGroupType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a EventTypeGroup entity.
     *
     * @Route("/{id}", name="admin_event_type_group_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:EventTypeGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventTypeGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing EventTypeGroup entity.
     *
     * @Route("/{id}/edit", name="admin_event_type_group_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:EventTypeGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventTypeGroup entity.');
        }

        $editForm = $this->createForm(new EventTypeGroupType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing EventTypeGroup entity.
     *
     * @Route("/{id}", name="admin_event_type_group_update")
     * @Method("PUT")
     * @Template("EventBundle:EventTypeGroup:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:EventTypeGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventTypeGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EventTypeGroupType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_type_group_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a EventTypeGroup entity.
     *
     * @Route("/{id}", name="admin_event_type_group_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EventBundle:EventTypeGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EventTypeGroup entity.');
            }

            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');

            $event = new EntityEvent();
            $event->setDispatcher($eventDispatcher);
            $event->setEntity($entity);
            $eventDispatcher->dispatch(EventEvents::EVENT_TYPE_GROUP_PRE_REMOVE, $event);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_event_type_group'));
    }

    /**
     * Creates a form to delete a EventTypeGroup entity by id.
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
