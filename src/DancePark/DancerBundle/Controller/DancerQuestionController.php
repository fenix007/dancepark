<?php
namespace DancePark\DancerBundle\Controller;

use DancePark\DancerBundle\Entity\SecretQuestion;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class DancerQuestionController
 * @package DancePark\DancerBundle\Controller
 */
class DancerQuestionController extends Controller
{
    /**
     * @Route("/edit/secret_question")
     * @Template()
     */
    public function editSecretQuestionAction()
    {
        if ($currentUser = $this->getUser()) {
            /** @var $questionRepo EntityRepository */
            $questionRepo = $this->get('doctrine')->getManager()->getRepository('DancerBundle:SecretQuestion');
            $questions = $questionRepo->findAll();
            $choices = array();

            foreach($questions as $question) {
                /** @var $question SecretQuestion */
                $choices[$question->getId()] = $question->getQuestion();
            }

            $formBuilder = $this->createFormBuilder($currentUser);
            $formBuilder->add('secretQuestion', 'choice', array(
                'label' => 'label.secret_question',
                'choices' => $choices,
            ));
            $formBuilder->add('plainSecretAnswer', 'text', array(
                'label' => 'label.secret_answer',
            ));

            $form = $formBuilder->getForm()->getViewData();
            return array(
                'secret_question' => $form
            );
        }
    }
}