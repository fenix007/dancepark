<?php

namespace DancePark\DancerBundle\Controller;

use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\DancerBundle\EventListener\Form\DancerEventSubscriber;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\DancerBundle\Entity\Dancer;
use DancePark\DancerBundle\Form\DancerType;

/**
 * Dancer controller.
 *
 * @Route("/dancer")
 */
class DancerController extends Controller
{

    /**
     * Lists all Dancer entities.
     *
     * @Route("/", name="admin_dancer")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DancerBundle:Dancer')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Dancer entity.
     *
     * @Route("/", name="admin_dancer_create")
     * @Method("POST")
     * @Template("DancerBundle:Dancer:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Dancer();
        $form = $this->createForm(
            new DancerType(
                new DancerEventSubscriber($this->get('fos_user.user_manager')),
                $this->get('doctrine')->getManager(),
                $this->get('dancer.dance_type.event_subscriber')
            ), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_dancer_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Dancer entity.
     *
     * @Route("/new", name="admin_dancer_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Dancer();
        $form   = $this->createForm(
            new DancerType(
                new DancerEventSubscriber($this->get('fos_user.user_manager')),
                $this->get('doctrine')->getManager(),
                $this->get('dancer.dance_type.event_subscriber')
            ), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Dancer entity.
     *
     * @Route("/{id}", name="admin_dancer_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:Dancer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dancer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Dancer entity.
     *
     * @Route("/{id}/edit", name="admin_dancer_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:Dancer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dancer entity.');
        }

        $editForm = $this->createForm(
            new DancerType(
                new DancerEventSubscriber($this->get('fos_user.user_manager')),
                $this->get('doctrine')->getManager(),
                $this->get('dancer.dance_type.event_subscriber')
            ), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Dancer entity.
     *
     * @Route("/{id}", name="admin_dancer_update")
     * @Method("PUT")
     * @Template("DancerBundle:Dancer:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:Dancer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dancer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(
            new DancerType(
                new DancerEventSubscriber($this->get('fos_user.user_manager')),
                $this->get('doctrine')->getManager(),
                $this->get('dancer.dance_type.event_subscriber')
            ), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_dancer_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Dancer entity.
     *
     * @Route("/{id}", name="admin_dancer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DancerBundle:Dancer')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Dancer entity.');
            }

            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch('dancer.pre_remove', $entityEvent);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_dancer'));
    }

    /**
     * Creates a form to delete a Dancer entity by id.
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
