<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use DancePark\FrontBundle\Component\EventManager\Filter\FilterInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\DancePark\EventBundle\Entity\EventType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

class EventTypeFilter extends AbstractFilter
{
    /** @var $types array*/
    protected $types;

    /**
     * @param QueryBuilder $queryBuilder
     * @param $types
     */
    public static function changeQueryBuilder(QueryBuilder $queryBuilder, $types)
    {
        $queryBuilder->andWhere('e.type IN (:event_types)');
        $queryBuilder->setParameter('event_types', $types);
    }

    /**
     * Validate Request parameter data? if exists
     *
     * @param $data mixed
     * @return bool
     */
    public function validateParameter($data)
    {
        if (isset($data['event_types']) && is_array($data['event_types']) && !empty($data['event_types'])) {
            $this->types = $data['event_types'];
            return true;
        }
        return false;
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
        $types = $this->types;

        self::changeQueryBuilder($queryBuilder, $types);
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
        $eventTypesRepo = $this->em->getRepository('EventBundle:EventType');

        $eventTypesOptions = array();
        $eventTypes = $eventTypesRepo->findAll();

        foreach ($eventTypes as $eType) {
            /** @var $eType EventType */
            $eventTypesOptions[$eType->getTypeGroup()->getName()][$eType->getId()] = $eType->getName() ;
        }

        $formBuilder
            ->add('event_types', 'choice', array(
                'label' => 'label.event_types',
                'choices' => $eventTypesOptions,
                'multiple' => true,
                'expanded' => true
            ));
    }
}