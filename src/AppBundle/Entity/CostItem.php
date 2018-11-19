<?php

namespace AppBundle\Entity;

use AppBundle\Model\Billable;
use AppBundle\Model\Item;
use AppBundle\Model\Priceable;
use AppBundle\Model\Timestampable;
use AppBundle\Model\Versionable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Service;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CostItem
 *
 * @ORM\Table(name="cost_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CostItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CostItem extends Item implements Billable, Versionable
{
    use Priceable, Timestampable;

    /**
     * @var boolean
     * @ORM\Column(name="discountable", type="boolean", nullable=false)
     */
    private $discountable = false;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Discount", mappedBy="costItems")
     * @ORM\JoinTable(name="item_discounts")
     */
    protected $discounts;


    public function __construct()
    {
        $this->discounts = new ArrayCollection();
    }

    /**
     * @return boolean
     */
    public function isDiscountable(): bool
    {
        return $this->discountable;
    }

    /**
     * @param boolean $discountable
     */
    public function setDiscountable(bool $discountable = false): void
    {
        $this->discountable = $discountable;
    }

    /**
     * @return Discount[]|null
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param mixed $discounts
     */
    public function addDiscount(Discount $discount): void
    {
        $this->discounts[] = $discount;
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
