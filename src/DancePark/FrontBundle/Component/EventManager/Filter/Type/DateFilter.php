<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use DancePark\EventBundle\Entity\DateRegularWeek;
use DancePark\EventBundle\Entity\Event;
use DancePark\FrontBundle\Component\EventManager\Filter\FilterInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class DateFilter extends AbstractFilter
{
    /** @var $dateFrom \DateTime */
    protected $dateFrom = null;
    /** @var $dateTo \DateTime */
    protected $dateTo = null;

    protected $setDateRegular = null;
    /**
     * Validate Request parameter data? if exists
     *
     * @param $data mixed
     * @return bool
     */
    public function validateParameter($data)
    {
        $include = false;
        if (isset($data['date_from']) && is_string($data['date_from'])) {
            if (strpos($data['date_from'], '/')) {
                $data['date_from'] = \DateTime::createFromFormat('m/d/Y', $data['date_from']);
            } else {
                $data['date_from'] = \DateTime::createFromFormat('Y-n-j', $data['date_from']);
            }
        }
        if (isset($data['date_to']) && is_string($data['date_to'])) {
            if (strpos($data['date_to'], '/')) {
                $data['date_to'] = \DateTime::createFromFormat('m/d/Y', $data['date_to']);
            } else {
                $data['date_to'] = \DateTime::createFromFormat('Y-n-j', $data['date_to']);
            }
        }

        if (isset($data['date_from'])
            && is_object($data['date_from'])
            && $data['date_from'] instanceof \DateTime
        ) {
            $include = true;
            $this->dateFrom = $data['date_from'];
        }
        if (isset($data['date_to'])
            && is_object($data['date_to'])
            && $data['date_to'] instanceof \DateTime
        ) {
            $include = true;
            $this->dateTo = $data['date_to'];
        }
        return $include;
    }



    public function filterDateRegular(QueryBuilder $qb, EntityManager $em, &$first = true)
    {
        if ($first) {
            $this->setDateRegular = $qb;
        }
        $first = false;
        if ($this->dateTo) {
        $currentDate = new \DateTime();

        $dateDiff = $currentDate->diff($this->dateTo);

        if ($dateDiff->invert == 0 && $dateDiff->days < 6) {
            $currentDay = $currentDate->format('w');
            if ($this->dateTo) {
                $lastDay = $this->dateTo->format('w');
            } else {
                $lastDay = $currentDay;
            }

            if ($this->dateFrom) {
                $firstDay = $this->dateFrom->format('w');
            } else {
                $firstDay = $currentDay;
            }

            if ($currentDay > $lastDay) {
                $startDay = 1;
                $endDay = 7;

                $qb->andWhere('
                        (date.dayOfWeek >= :start_day
                            AND date.dayOfWeek <= :last_day)
                        OR (date.dayOfWeek >= :first_day
                            AND date.dayOfWeek <= :end_day)
                        ');
                $qb->setParameter('start_day', $startDay);
                $qb->setParameter('end_day', $endDay);

            } else {
                $qb->andWhere('(date.dayOfWeek <= :last_day
                        AND date.dayOfWeek >= :first_day)');
            }

            $qb->setParameter('last_day', $lastDay);
            $qb->setParameter('first_day', $firstDay);
            }
        }
    }

    /**
     * Provider filter action
     *
     * @param QueryBuilder $queryBuilder
     * @param mixed $data
     * @param Request $request
     *
     * @return mixed
     */
    public function applyFilter(QueryBuilder $queryBuilder, $data, Request $request = null)
    {
        $dateFrom = $this->dateFrom;
        $dateTo = $this->dateTo;

        $this->changeQueryBuilder($queryBuilder, $dateTo, $dateFrom, $this->setDateRegular);
    }

    /**
     * Add filter widget to the form while form building
     *
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return Form
     */
    public function editFilterForm(FormBuilder $formBuilder, Request $request = null)
    {
        $formBuilder
            ->add('date', 'text', array(
                'label' => 'label.date',
                'required' => false,
            ))
            ->add('date_from', 'text', array(
                'label' => 'label.from',
                'required' => false
            ))
            ->add('date_to', 'text', array(
                'label' => 'label.to',
                'required' => false,
            ));
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param DateTime $dateTo
     * @param DateTime $dateFrom
     */
    public static  function changeQueryBuilder(QueryBuilder $queryBuilder, \DateTime $dateTo = null, \DateTime $dateFrom = null, $dateRegular = null)
    {
        if ($dateTo) {
            $joined = static::checkJoin($queryBuilder, 'dr');
            if (!$joined) {
                $queryBuilder->leftJoin('e.dateRegular', 'dr', 'WITH');
            }

            $currentDate = new \DateTime();

            $dateDiff = $currentDate->diff($dateTo);

            if ($dateDiff->invert == 0 && $dateDiff->days < 6) {
                $currentDay = $currentDate->format('w');
                if ($dateTo) {
                    $lastDay = $dateTo->format('w');
                } else {
                    $lastDay = $currentDay;
                }

                if ($dateFrom) {
                    $firstDay = $dateFrom->format('w');
                } else {
                    $firstDay = $currentDay;
                }

                if ($currentDay > $lastDay) {
                    $startDay = 1;
                    $endDay = 7;
                    $queryBuilder->setParameter('start_day', $startDay);
                    $queryBuilder->setParameter('end_day', $endDay);
                }
                $queryBuilder->setParameter('last_day', $lastDay);
                $queryBuilder->setParameter('first_day', $firstDay);
            }

            $or = $queryBuilder->expr()->orX();
            $or->add('e.kind = :kind_1 AND e.date <= :date_to');
            if ($dateRegular) {
                $and = $queryBuilder->expr()->andX();
                $and->add('e.kind = :kind_2');
                $and->add($queryBuilder->expr()->in(
                    'dr.id',
                    $dateRegular
                        ->getDQL()
                ));
                $or->add($and);
            } else {
                $or->add('e.kind = :kind_2');
            }
            $queryBuilder->andWhere($or);
            $queryBuilder->setParameter('date_to', $dateTo->format('Y-m-d'));
        }

        if ($dateFrom) {
            $queryBuilder->andWhere('((e.kind = :kind_1 AND e.date >= :date_from) OR (e.kind = :kind_2))');
            $queryBuilder->setParameter('date_from', $dateFrom->format('Y-m-d'));
        }

        $queryBuilder->setParameter('kind_1', Event::EVENT_KIND_SINGLE);
        $queryBuilder->setParameter('kind_2', Event::EVENT_KIND_REGULAR);
    }
}