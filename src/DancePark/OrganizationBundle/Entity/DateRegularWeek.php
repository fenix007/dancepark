<?php

namespace DancePark\OrganizationBundle\Entity;

use DancePark\CommonBundle\Entity\DateRegularAbstract;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * DateRegularWeek
 *
 * @ORM\Table(name="date_regular_week_organization")
 * @ORM\Entity(repositoryClass="DancePark\OrganizationBundle\Entity\DateRegularWeekRepository")
 * @Assert\Callback(methods={"validateDayOfWeek"})
 */
class DateRegularWeek extends DateRegularAbstract
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
     * @ORM\ManyToOne(
     *      targetEntity="DancePark\OrganizationBundle\Entity\Organization",
     *      inversedBy="dateRegular",
     *      cascade={"merge", "persist"})
     */
    private $organization;


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
     * Set organization
     *
     * @param \DancePark\OrganizationBundle\Entity\Organization $organization
     * @return DateRegularWeek
     */
    public function setOrganization(\DancePark\OrganizationBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;
    
        return $this;
    }

    /**
     * Get organization
     *
     * @return \DancePark\OrganizationBundle\Entity\Organization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}