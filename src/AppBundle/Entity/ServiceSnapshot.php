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
 * @ORM\Table(name="service_snapshot", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="version_unique", columns={"service_id", "version"})
 * })
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CategorySnapshot", inversedBy="serviceSnapshots", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="categorySnapshot_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $categorySnapshot;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ItemSnapshot", mappedBy="serviceSnapshot", cascade={"persist", "remove"})
     */
    protected $itemSnapshots;


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

    /**
     * @return ItemSnapshot[] | null
     */
    public function getItemSnapshots()
    {
        return $this->itemSnapshots;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }
}

