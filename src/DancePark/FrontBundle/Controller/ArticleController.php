<?php

namespace DancePark\FrontBundle\Controller;

use DancePark\EventBundle\Entity\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleController extends Controller
{
    const COUNT_ARTICLE_PER_PAGE = 10;
    /**
     * @Route("article/{article}", name="article_view")
     * @ParamConverter("article", class="CommonBundle:Article")
     * @Template(vars={"article"})
     */
    public function viewAction()
    {
    }

    /**
     * @Route("article", name="article_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        /** @var $articleRepo EventRepository */
        $articleRepo = $this->get('doctrine')->getManager()->getRepository('CommonBundle:Article');

        $page = $request->get('page', 0);
        $offset = $page * static::COUNT_ARTICLE_PER_PAGE;

        $isNext = false;
        $isPrev = (bool)$page;
        try {
            $list = $articleRepo->findBy(array(), array('id' => 'DESC'), static::COUNT_ARTICLE_PER_PAGE + 1, $offset);
        } catch(NotFoundHttpException $e) {
            $list = array();
        }

        if (count($list) > static::COUNT_ARTICLE_PER_PAGE) {
            $isNext = true;
        }

        return array(
            'list' => array_slice($list, 0, static::COUNT_ARTICLE_PER_PAGE),
            'is_next' => $isNext,
            'is_prev' => $isPrev,
            'page' => $page
        );
    }
}