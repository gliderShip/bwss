<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Service
 *
 * @ORM\Table(name="service_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceCategoryRepository")
 */
class ServiceCategory
{
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Service", mappedBy="serviceCategory", cascade={"persist", "remove"})
     */
    protected $services;


    public function __construct() {
        $this->services = new ArrayCollection();
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
    public function setName($name): ServiceCategory
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
     * @return ArrayCollection|null
     */
    public function getServices()
    {
        return $this->services;
    }

//    /**
//     * @param mixed $services
//     */
//    public function setServices($services): void
//    {
//        $this->services = $services;
//    }

    public function __toString()
    {
        return (string) $this->getName();
    }


}

