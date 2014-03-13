<?php

namespace DancePark\OrganizationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\OrganizationBundle\Entity\DateRegularWeek;
use DancePark\OrganizationBundle\Form\DateRegularWeekType;

/**
 * DateRegularWeek controller.
 *
 * @Route("/date_regular_week")
 */
class DateRegularWeekController extends Controller
{
    /**
     * Lists all DateRegularWeek entities.
     *
     * @Route("/", name="date_regular_week")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OrganizationBundle:DateRegularWeek')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new DateRegularWeek entity.
     *
     * @Route("/", name="date_regular_week_create")
     * @Method("POST")
     * @Template("OrganizationBundle:DateRegularWeek:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new DateRegularWeek();
        $form = $this->createForm(new DateRegularWeekType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('date_regular_week_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new DateRegularWeek entity.
     *
     * @Route("/new", name="date_regular_week_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new DateRegularWeek();
        $form   = $this->createForm(new DateRegularWeekType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a DateRegularWeek entity.
     *
     * @Route("/{id}", name="date_regular_week_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrganizationBundle:DateRegularWeek')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DateRegularWeek entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing DateRegularWeek entity.
     *
     * @Route("/{id}/edit", name="date_regular_week_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrganizationBundle:DateRegularWeek')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DateRegularWeek entity.');
        }

        $editForm = $this->createForm(new DateRegularWeekType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing DateRegularWeek entity.
     *
     * @Route("/{id}", name="date_regular_week_update")
     * @Method("PUT")
     * @Template("OrganizationBundle:DateRegularWeek:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrganizationBundle:DateRegularWeek')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DateRegularWeek entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DateRegularWeekType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('date_regular_week_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a DateRegularWeek entity.
     *
     * @Route("/{id}", name="date_regular_week_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrganizationBundle:DateRegularWeek')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DateRegularWeek entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('date_regular_week'));
    }

    /**
     * Creates a form to delete a DateRegularWeek entity by id.
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
