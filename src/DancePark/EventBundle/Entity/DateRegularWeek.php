<?php

namespace DancePark\EventBundle\Entity;

use DancePark\CommonBundle\Entity\DateRegularAbstract;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * DateRegularWeek
 *
 * @ORM\Table(name="date_regular_week")
 * @ORM\Entity(repositoryClass="DancePark\EventBundle\Entity\DateRegularWeekRepository")
 * @Assert\Callback(methods={"validateDayOfWeek"})
 */
class DateRegularWeek  extends  DateRegularAbstract
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\EventBundle\Entity\Event", inversedBy="dateRegular", cascade={"merge", "persist"})
     */
    private $event;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set event
     *
     * @param integer $event
     * @return DateRegularWeek
     */
    public function setEvent($event)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return integer 
     */
    public function getEvent()
    {
        return $this->event;
    }
}
