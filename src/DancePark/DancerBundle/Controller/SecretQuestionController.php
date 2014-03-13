<?php

namespace DancePark\DancerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\DancerBundle\Entity\SecretQuestion;
use DancePark\DancerBundle\Form\SecretQuestionType;

/**
 * SecretQuestion controller.
 *
 * @Route("/secret_question")
 */
class SecretQuestionController extends Controller
{
    /**
     * Lists all SecretQuestion entities.
     *
     * @Route("/", name="secret_question")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DancerBundle:SecretQuestion')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new SecretQuestion entity.
     *
     * @Route("/", name="secret_question_create")
     * @Method("POST")
     * @Template("DancerBundle:SecretQuestion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new SecretQuestion();
        $form = $this->createForm(new SecretQuestionType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('secret_question_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new SecretQuestion entity.
     *
     * @Route("/new", name="secret_question_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new SecretQuestion();
        $form   = $this->createForm(new SecretQuestionType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a SecretQuestion entity.
     *
     * @Route("/{id}", name="secret_question_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:SecretQuestion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SecretQuestion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing SecretQuestion entity.
     *
     * @Route("/{id}/edit", name="secret_question_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:SecretQuestion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SecretQuestion entity.');
        }

        $editForm = $this->createForm(new SecretQuestionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing SecretQuestion entity.
     *
     * @Route("/{id}", name="secret_question_update")
     * @Method("PUT")
     * @Template("DancerBundle:SecretQuestion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:SecretQuestion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SecretQuestion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SecretQuestionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('secret_question_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a SecretQuestion entity.
     *
     * @Route("/{id}", name="secret_question_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DancerBundle:SecretQuestion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SecretQuestion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('secret_question'));
    }

    /**
     * Creates a form to delete a SecretQuestion entity by id.
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
