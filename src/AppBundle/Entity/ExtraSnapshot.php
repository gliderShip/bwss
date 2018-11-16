<?php

namespace AppBundle\Entity;

use AppBundle\Model\Billable;
use AppBundle\Model\Priceable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\CategorySnapshot;
use AppBundle\Entity\Extra;

/**
 * Extra
 *
 * @ORM\Table(name="extra_snapshot", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="version_unique", columns={"extra_id", "version"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ExtraSnapshotRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ExtraSnapshot extends AbstractSnapshot implements Billable
{
    use Priceable ;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Extra")
     * @ORM\JoinColumn(name="extra_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $extra;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CategorySnapshot", inversedBy="extraSnapshots", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="categorySnapshot_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $categorySnapshot;


    public function __construct(Extra $extra, int $version)
    {
        parent::__construct($version);

        $this->extra = $extra;
        $this->setName($extra->getName());
        $this->price = $extra->getPrice();
        $this->priceType = $extra->getPriceType();
    }

    /**
     * @return Extra|null
     */
    public function getExtra()
    {
        return $this->extra;
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

