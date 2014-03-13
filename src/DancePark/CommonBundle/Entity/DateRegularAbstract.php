<?php
namespace DancePark\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @ORM\MappedSuperclass
 * Class DateRegularAbstract
 * @package DancePark\CommonBundle\Entity
 */
abstract class DateRegularAbstract
{


    /**
     * @var integer
     *
     * @ORM\Column(name="day_of_week", type="smallint")
     * @Assert\Range(
     *      min=1,
     *      max=7,
     *      minMessage="error.date_regular_week.day_of_week.min",
     *      maxMessage="error.date_regular_week.day_of_week.max"
     *  )
     */
    private $dayOfWeek;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="time")
     * @Assert\Time(message="error.data_regular_week.start_time")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="time")
     * @Assert\Time(message="error.date_regular_week.end_time")
     */
    private $endTime;

    /**
     * Get week day name
     */
    public function getWeekDayName()
    {
        $days = self::getDaysOfWeek();

        return $days[$this->getDayOfWeek()];
    }


    /**
     * Set dayOfWeek
     *
     * @param integer $dayOfWeek
     * @return DateRegularAbstract
     */
    public function setDayOfWeek($dayOfWeek)
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    /**
     * Get dayOfWeek
     *
     * @return integer
     */
    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return DateRegularAbstract
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return DateRegularAbstract
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    public static function getDaysOfWeek($keysOnly = false)
    {
        $days = array(
            1 => 'label.week_day.monday',
            2 => 'label.week_day.tuesday',
            3 => 'label.week_day.wednesday',
            4 => 'label.week_day.thursday',
            5 => 'label.week_day.friday',
            6 => 'label.week_day.saturday',
            7 => 'label.week_day.sunday'
        );

        if ($keysOnly) {
            return array_keys($days);
        } else {
            return $days;
        }
    }


    /**
     * Get days of week
     */
    public function validateDayOfWeek(ExecutionContext $context)
    {
        if (!in_array($this->getDayOfWeek(), self::getDaysOfWeek(true))) {
            $context->addViolationAt('dayOfWeek', 'error.dayOfWeek', array(), null);
        }
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        $currentDate = new \DateTime();
        $cDay = $currentDate->format('w');
        /** @var $dateRegular DateRegularAbstract */
        $div = $this->getDayOfWeek() - $cDay;
        if ($div < 0) {
            $div = $dateRegular->getDayOfWeek() + 7 - $cDay;
        }

        $nextDate = $currentDate->add(new \DateInterval('P' . $div . 'D'));
        return $nextDate;
    }
}