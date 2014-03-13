<?php
namespace DancePark\FrontBundle\Controller;

use DancePark\FrontBundle\Form\Type\DigestType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DigestController extends Controller
{
    /**
     * Return digest form
     *
     * @Route("/front/get_digest_form", name="get_digest_form")
     * @Template()
     */
    function formAction(Request $request)
    {
        $form = $this->createForm(new DigestType($this->getUser()));
        $form->bind($request);

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Create digest
     *
     * @Route("/front/create_digest", name="create_digest")
     */
    public function createDigestAction(Request $request)
    {
        $form = $this->createForm(new DigestType($this->getUser()));
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $data = $form->getData();
            $curDate = new \DateTime();
            $email = $data->getEmail();
            if (!$email && $data->getDancer()) {
                $email = $data->getDancer()->getEmail();
            } else {
                return new JsonResponse(array('error' => 1, 'success' => 0));
            }
            $data->setHash(md5($curDate->getTimestamp() . $email));

            $em->persist($data);
            $em->flush();
            return new Response();
        } else {
            return $this->render('FrontBundle:Digest:form.html.twig', $this->formAction($request));
        }
    }

    /**
     * Delete digest
     *
     * @Route("/digest/dance/delete&hash={hash}", name="drop_user_digest")
     */
    public function deleteDigest($hash)
    {
        $em = $this->get('doctrine')->getManager();
        $digestRepo = $em->getRepository('DancerBundle:Digest');

        if (!$digests = $digestRepo->findBy(array('hash' => $hash))){
            throw $this->createNotFoundException();
        }

        foreach($digests as $digest) {
           $em->remove($digest);
        }
        $em->flush();
        return new RedirectResponse($this->get('router')->getGenerator()->generate('front'));
    }
}