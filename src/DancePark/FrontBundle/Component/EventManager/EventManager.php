<?php
namespace DancePark\FrontBundle\Component\EventManager;

use DancePark\CommonBundle\Entity\FilterHash;
use DancePark\FrontBundle\Component\EventManager\Filter\FilterInterface;
use DancePark\FrontBundle\Component\EventManager\Filter\Type\AbstractFilter;
use DancePark\FrontBundle\Component\EventManager\Filter\Type\DateFilter;
use DancePark\FrontBundle\Component\EventManager\Filter\Type\QuickFilter;
use DancePark\FrontBundle\Component\ServicesBag\ServicesBag;
use Doctrine\ORM\QueryBuilder;
use \Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

/**
 * Class event list worker
 * @package DancePark\FrontBundle\Component\EventManager
 */
class EventManager
{
    /** @var $form Form */
    protected $form;

    /** @var $quickForm Form */
    protected $quickForm;

    /** @var $availableFilters array */
    protected $availableFilters;

    protected $emptyValueFilters;

    /** @var $filtersBag ServicesBag */
    protected $filtersBag;

    /** @var $data object */
    protected $data;

    /** @var $em EntityManager */
    protected $em;

    /** @var $hash FilterHash */
    protected $hash;

    /**
     * Create defaults for normal functionality
     *
     * @param FormBuilder $formBuilder
     * @param Request $request
     */
    public function __construct(EntityManager $em, FormFactory $formFactory, ServicesBag $bag, Request $request)
    {
        $this->availableFilters = array();
        $this->emptyValueFilters = array();

        $this->filtersBag = $bag;

        $this->em = $em;

        $this->getFilterForm($formFactory->createBuilder('form', null, array()), $request);
        $this->getQuickFilterForm($formFactory->createBuilder('form', null, array()), $request);

        if ($request->getMethod() == 'POST') {
            $this->form->bind($request);
            $this->quickForm->bind($request);

            $data = $this->quickForm->getData();
            if (!empty($data)) {
                $this->data = array_merge($this->form->getData(), $data);
            } else {
                $this->data = $data;
            }
        } else if ($request->get('hash', false)) {
            $hash = $request->get('hash');
            /** @var $hash FilterHash */
            $hash = $this->em->getRepository('CommonBundle:FilterHash')->findOneBy(array('hash' => $hash));
            if ($hash) {
                $this->data = unserialize($hash->getFilters());
            }
        } else if ($request->getMethod() == 'GET') {
            $this->data = $request->query->all();
        } else {
            $this->data = array();
        }
    }

    /**
     * Set available filters
     *
     * @param Request $request
     */
    protected function filtersMapper()
    {
        foreach ($this->filtersBag as $filter) {
            /** @var $filter FilterInterface */
            if ($filter->validateParameter($this->data)) {
                  $this->availableFilters[] = $filter;
            } else {
                $this->emptyValueFilters[] = $filter;
            }
        }
    }

    public function getRecommendedEvents()
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->distinct()
            ->select('e')
            ->from('EventBundle:Event', 'e')
            ->leftJoin('EventBundle:EventClosing', 'ec', 'WITH', 'ec.event = e.id')
            ->where('ec.id IS NULL')
            ->where('e.recommended = :recommended')
            ->setParameter('recommended', true)
            ->setMaxResults(20);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Filters apply
     */
    protected function applyFilters(QueryBuilder $queryBuilder, Request $request = null)
    {
        foreach ($this->availableFilters as $filter) {
             /** @var $filter FilterInterface */
            $filter->applyFilter($queryBuilder, $this->data, $request);
        }
        foreach ($this->emptyValueFilters as $filter) {
            /** @var $filter FilterInterface */
            $filter->applyEmpty($queryBuilder, $request);
        }
    }

    /**
     * Filter searched data
     */
    protected function checkResults(array &$results)
    {
        foreach ($this->availableFilters as $filter) {
            /** @var $filter FilterInterface */
            $filter->postExecuteFilter($results, $this->data);
        }
    }

    /**
     * Get list of Events by request parameters
     *
     * @param Request $request
     */
    public function getEventsByRequest(&$isNext = false, Request $request = null, $page = 0)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->distinct()
            ->select('e')
            ->from('EventBundle:Event', 'e')
            ->leftJoin('EventBundle:EventClosing', 'ec', 'WITH', 'ec.event = e.id')
            ->where('ec.id IS NULL');
        $this->filtersMapper($request);

        $dateRegularSubQuery = $this->em->createQueryBuilder();
        $dateRegularSubQuery
            ->select('date.id')
            ->from('EventBundle:DateRegularWeek', 'date');
        
        $first = true;
        foreach($this->availableFilters as $filter) {
            /** @var $filter AbstractFilter */
            $filter->filterDateRegular($dateRegularSubQuery, $this->em, $first);
        }

        $this->applyFilters($queryBuilder, $request);

        $results = $queryBuilder
            ->setMaxResults(FilterInterface::PAGE_COUNT_RESULT + 1)
            ->setFirstResult($page * FilterInterface::PAGE_COUNT_RESULT)
            ->addOrderBy('e.checkDate', 'DESC')
            ->getQuery()
            ->getResult();
        $this->checkResults($results);

        if (count($results) > FilterInterface::PAGE_COUNT_RESULT) {
            $isNext = true;
            unset($results[FilterInterface::PAGE_COUNT_RESULT]);
        }

        if (count($results) > 0) {
            $filters = serialize($this->data);
            $hash = substr(md5($filters), 0, 64);
            if  (!$this->em->getRepository('CommonBundle:FilterHash')->findBy(array('hash' => $hash))) {
                $hashObj = new FilterHash();
                $hashObj->setFilters($filters);
                $hashObj->setHash($hash);
                $this->em->persist($hashObj);
                $this->em->flush();
            }
            $this->hash = $hash;
        }

        return $results;
    }

    public function getStreetBounds($data = array())
    {
        if (empty($data)) {
            $data = $this->data;
        }

        /** @var $dateFilter DateFilter */
        $addressFilter = $this->filtersBag->get('front.event_filter.address');
        if ($addressFilter->validateParameter($data)) {

            $bounds = $addressFilter->getBounds($data);
            return $bounds;
        }

        return null;
    }

    /**
     * @param $data
     * @param int $page
     * @return array
     */
    public function getEventsByData(&$isNext = false, $data, $page = 0)
    {
        $this->data = $data;

        return $this->getEventsByRequest($isNext, null, $page);
    }

    /**
     * Build filters form
     *
     * @param Request $request
     *
     * @return Form
     */
    public function getFilterForm(FormBuilder $formBuilder, Request $request = null)
    {
        foreach ($this->filtersBag as $filter) {
            /** @var $filter FilterInterface */
            $filter->editFilterForm($formBuilder, $request);
        }
        $this->form = $formBuilder->getForm();

        return $this->form;
    }

    public function getQuickFilterForm(FormBuilder $formBuilder, Request $request = null)
    {
        /** @var $quickFilter QuickFilter */
        $quickFilter = $this->getFiltersBag()->get('front.event_filter.quick');

        $quickFilter->editFilterForm($formBuilder, $request, 'quick_filter');

        $this->quickForm = $formBuilder->getForm();

        return $this->quickForm;
    }

    /**
     * Get events for today
     */
    public function getTodayEvents(&$isNext = false, $page = 0)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->distinct()
            ->select('e')
            ->from('EventBundle:Event', 'e');
        /** @var $dateFilter DateFilter */
        $dateFilter = $this->filtersBag->get('front.event_filter.date');
        $results = array();
        $dateRegularSubQuery = $this->em->createQueryBuilder();
        $dateRegularSubQuery
            ->select('date.id')
            ->from('EventBundle:DateRegularWeek', 'date');

        $first = true;

        if ($dateFilter->validateParameter(array('date_from' => new \DateTime(), 'date_to' => new \DateTime()))) {
            $dateFilter->filterDateRegular($dateRegularSubQuery, $this->em, $first);
            $dateFilter->applyFilter($queryBuilder, array());

            $results = $queryBuilder
                ->setMaxResults(FilterInterface::PAGE_COUNT_RESULT + 1)
                ->setFirstResult($page * FilterInterface::PAGE_COUNT_RESULT)
                ->getQuery()
                ->getResult();
            if (count($results) > FilterInterface::PAGE_COUNT_RESULT) {
                $isNext = true;
            }
        }
        return $results;
    }

    /**
     * @param array $availableFilters
     */
    public function setAvailableFilters($availableFilters)
    {
        $this->availableFilters = $availableFilters;
    }

    /**
     * @return array
     */
    public function getAvailableFilters()
    {
        return $this->availableFilters;
    }

    /**
     * @param object $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * @param \DancePark\FrontBundle\Component\ServicesBag\ServicesBag $filtersBag
     */
    public function setFiltersBag($filtersBag)
    {
        $this->filtersBag = $filtersBag;
    }

    /**
     * @return \DancePark\FrontBundle\Component\ServicesBag\ServicesBag
     */
    public function getFiltersBag()
    {
        return $this->filtersBag;
    }

    /**
     * @param \Symfony\Component\Form\Form $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function getForm()
    {
        if (!$this->form->isBound() && $this->data) {
            $this->form->setData($this->data);
        }
        return $this->form;
    }

    /**
     * @param \Symfony\Component\Form\Form $quickForm
     */
    public function setQuickForm($quickForm)
    {
        $this->quickForm = $quickForm;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function getQuickForm()
    {
        return $this->quickForm;
    }

    /**
     * @param \DancePark\CommonBundle\Entity\FilterHash $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return \DancePark\CommonBundle\Entity\FilterHash
     */
    public function getHash()
    {
        return $this->hash;
    }


}