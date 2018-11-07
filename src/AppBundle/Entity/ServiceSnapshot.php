<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\CategorySnapshot;
use AppBundle\Entity\Service;

/**
 * Service
 *
 * @ORM\Table(name="service_snapshot")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceSnapshotRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ServiceSnapshot extends AbstractSnapshot
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Service")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $service;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CategorySnapshot", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="categorySnapshot_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $categorySnapshot;


    public function __construct(Service $service, int $version)
    {
        parent::__construct($version);

        $this->service = $service;
        $this->setName($service->getName());
    }

    /**
     * @return Service|null
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param CategorySnapshot $categorySnapshot
     */
    public function setCategorySnapshot(CategorySnapshot $categorySnapshot): void
    {
        $this->categorySnapshot = $categorySnapshot;
    }

    /**
     * @return CategorySnapshot
     */
    public function getCategorySnapshot()
    {
        return $this->categorySnapshot;
    }

    public function __toString()
    {
        return $this->name . ' Snapshot';
    }

}

