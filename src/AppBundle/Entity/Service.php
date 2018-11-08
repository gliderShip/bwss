<?php

namespace AppBundle\Entity;

use AppBundle\Model\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Service
 *
 * @ORM\Table(name="service")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Service
{
    use Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceCategory", inversedBy="services")
     * @ORM\JoinColumn(name="serviceCategory_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $serviceCategory;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CostItem", mappedBy="service", cascade={"persist", "remove"})
     */
    protected $items;

    
    public function __construct() {

        $this->items = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Service
     */
    public function setName($name): Service
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param $serviceCategory
     * @return $this
     */
    public function setServiceCategory(ServiceCategory $serviceCategory): Service
    {
        $this->serviceCategory = $serviceCategory;

        return $this;
    }

    /**
     * @return Service|null
     */
    public function getServiceCategory(): ?ServiceCategory
    {
        return $this->serviceCategory;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

}

