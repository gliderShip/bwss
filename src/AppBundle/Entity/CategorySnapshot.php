<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Service
 *
 * @ORM\Table(name="category_snapshot", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="version_unique", columns={"category_id", "version"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategorySnapshotRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CategorySnapshot extends AbstractSnapshot
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ServiceSnapshot", mappedBy="categorySnapshot", cascade={"persist", "remove"})
     */
    protected $serviceSnapshots;


    public function __construct(ServiceCategory $category, int $version)
    {
        parent::__construct($version);

        $this->category = $category;
        $this->setName($category->getName());
    }

    /**
     * @return ServiceCategory|null
     */
    public function getCategory(): ?ServiceCategory
    {
        return $this->category;
    }

    /**
     * @return ServiceSnapshot[] | null
     */
    public function getServiceSnapshots()
    {
        return $this->serviceSnapshots;
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

