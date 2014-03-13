<?php

namespace DancePark\CommonBundle\Controller;

use DancePark\CommonBundle\EventListener\Event\CommonEvents;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\CommonBundle\EventListener\SetMetroStationEventSubscriber;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\CommonBundle\Entity\Place;
use DancePark\CommonBundle\Form\PlaceType;

/**
 * Place controller.
 *
 * @Route("/place")
 */
class PlaceController extends Controller
{

    /**
     * Lists all Place entities.
     *
     * @Route("/", name="place")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CommonBundle:Place')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Place entity.
     *
     * @Route("/", name="place_create")
     * @Method("POST")
     * @Template("CommonBundle:Place:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Place();
        $form = $this->createForm(new PlaceType(new SetMetroStationEventSubscriber($this->get('doctrine')), $this->get('doctrine')->getEntityManager()), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch(CommonEvents::PLACE_PRE_PERSIST, $entityEvent);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('place_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Place entity.
     *
     * @Route("/new", name="place_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Place();
        $entity->setAddrGroup($this->get('doctrine')->getEntityManager()->getRepository("CommonBundle:AddressGroup")->find(5));
        $form   = $this->createForm(new PlaceType(new SetMetroStationEventSubscriber($this->get('doctrine')), $this->get('doctrine')->getEntityManager()), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Place entity.
     *
     * @Route("/{id}", name="place_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Place entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Place entity.
     *
     * @Route("/{id}/edit", name="place_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Place entity.');
        }

        $editForm = $this->createForm(new PlaceType(new SetMetroStationEventSubscriber($this->get('doctrine')), $this->get('doctrine')->getEntityManager()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Place entity.
     *
     * @Route("/{id}", name="place_update")
     * @Method("PUT")
     * @Template("CommonBundle:Place:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Place entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PlaceType(new SetMetroStationEventSubscriber($this->get('doctrine')), $this->get('doctrine')->getEntityManager()), $entity);
        $editForm->bind($request);



        if ($editForm->isValid()) {
            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch(CommonEvents::PLACE_PRE_UPDATE, $entityEvent);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('place_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Place entity.
     *
     * @Route("/{id}", name="place_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CommonBundle:Place')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Place entity.');
            }

            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch(CommonEvents::PLACE_PRE_REMOVE, $entityEvent);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('place'));
    }

    /**
     * Creates a form to delete a Place entity by id.
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
