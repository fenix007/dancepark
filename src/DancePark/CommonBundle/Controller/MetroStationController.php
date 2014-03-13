<?php

namespace DancePark\CommonBundle\Controller;

use DancePark\CommonBundle\EventListener\Event\CommonEvents;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\CommonBundle\Entity\MetroStation;
use DancePark\CommonBundle\Form\MetroStationType;

/**
 * MetroStation controller.
 *
 * @Route("/metro_station")
 */
class MetroStationController extends Controller
{

    /**
     * Lists all MetroStation entities.
     *
     * @Route("/", name="metro_station")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CommonBundle:MetroStation')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new MetroStation entity.
     *
     * @Route("/", name="metro_station_create")
     * @Method("POST")
     * @Template("CommonBundle:MetroStation:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new MetroStation();
        $form = $this->createForm(new MetroStationType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('metro_station_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new MetroStation entity.
     *
     * @Route("/new", name="metro_station_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MetroStation();
        $form   = $this->createForm(new MetroStationType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a MetroStation entity.
     *
     * @Route("/{id}", name="metro_station_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:MetroStation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetroStation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MetroStation entity.
     *
     * @Route("/{id}/edit", name="metro_station_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:MetroStation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetroStation entity.');
        }

        $editForm = $this->createForm(new MetroStationType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing MetroStation entity.
     *
     * @Route("/{id}", name="metro_station_update")
     * @Method("PUT")
     * @Template("CommonBundle:MetroStation:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:MetroStation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetroStation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MetroStationType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('metro_station_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a MetroStation entity.
     *
     * @Route("/{id}", name="metro_station_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CommonBundle:MetroStation')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MetroStation entity.');
            }

            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch(CommonEvents::METRO_STATION_PRE_REMOVE, $entityEvent);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('metro_station'));
    }

    /**
     * Creates a form to delete a MetroStation entity by id.
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
