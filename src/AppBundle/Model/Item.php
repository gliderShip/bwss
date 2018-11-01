<?php

namespace AppBundle\Model;

use AppBundle\Entity\Service;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Item
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Service", inversedBy="items")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $service;

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
     * @return Service
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService(Service $service): void
    {
        $this->service = $service;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}

