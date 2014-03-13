<?php
namespace DancePark\FrontBundle\Component;

use DancePark\FrontBundle\Component\EventManager\EventManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class RenderInfo
{
    /** @var $container ContainerInterface */
    protected $container;

    /** @var $eventManager EventManager */
    protected $eventManager;

    /**
     * Set object defaults
     *
     * @param Registry $doctrine
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $request = new Request();
        if ($this->container->has('request')) {
            $request = $this->container->get('request');
        }
        $this->eventManager = new EventManager(
            $container->get('doctrine')->getManager(),
            $container->get('form.factory'),
            $container->get('services_bag.filters_container'),
            $request
        );
    }

    public function getCities()
    {
        $addressGroupRepo = $this->container->get('doctrine')->getManager()->getRepository('CommonBundle:AddressGroup');
        $cities = $addressGroupRepo->findBy(array('lvl' => 0));
        $res = array();
        foreach ($cities as $city) {
            $res[$city->getId()] = $city->getName();
        }
        return $res;
    }

    public function getUser()
    {
        /** @var $token AbstractToken */
        $token = $this->container->get('security.context')->getToken();
        if ($token !== null && is_object($token)) {
            if (is_object($token->getUser())){
                return $token->getUser();
            } else {
                return null;
            }
        }
        return null;
    }

    public function getQuickForm()
    {
        $quickForm = $this->eventManager->getQuickForm()->createView();
        $children = $quickForm->vars['form']->getChildren();
        $children['q']->vars['id'] = 'quick_filter';

        return $quickForm;
    }

    public function getQuickLinks()
    {
        // Links for quick filter
        $quickLinks = array(
            'label.disco',
            'label.salsa',
            'label.latina',
            'label.festival'
        );
        return $quickLinks;
    }

    public function getPagesMenu()
    {
        /** @var $pagesRepo EntityRepository */
        $pagesRepo = $this
          ->container
          ->get('doctrine')
          ->getManager()
          ->getRepository('CommonBundle:Page');

        $pages = $pagesRepo->findBy(array('active' => true));
        $routing = $this->container->get('router');
        $links = array();
        foreach($pages as $page) {
            $links[$page->getTitle()] = $routing->generate('page_action', array('path' => $page->getPath()));
        }
        return $links;
    }

    public function getTwitter()
    {
        return $this->container->getParameter('twitter');
    }
    public function getFb()
    {
        return $this->container->getParameter('fb');
    }
    public function getVk()
    {
        return $this->container->getParameter('vk');
    }
    public function getMail()
    {
        return 'mailto:' . $this->container->getParameter('mail');
    }
    public function getYt()
    {
        return $this->container->getParameter('yt');
    }
}