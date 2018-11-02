<?php

namespace AppBundle\Entity;

use AppBundle\Model\Billable;
use AppBundle\Model\Item;
use AppBundle\Model\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Service;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Offer;

/**
 * OfferItem
 *
 * @ORM\Table(name="offer_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfferItemRepository")
 */
class OfferItem
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
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="items")
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $offer;

    /**
     * @var integer
     * @Assert\GreaterThanOrEqual(0, message="This value should be positive")
     * @ORM\Column(name="hours", type="integer", options={"unsigned"=true}, nullable=false)
     */
    protected $hours;

    /**
     * @var ItemSnapshot
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ItemSnapshot", cascade={"persist"})
     * @ORM\JoinColumn(name="snapshot_id", referencedColumnName="id", nullable=false)
     */
    protected $itemSnapshot;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Offer
     */
    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    /**
     * @param Offer $offer
     */
    public function setOffer(Offer $offer): void
    {
        $this->offer = $offer;
    }


    /**
     * @return string
     */
    public function getHours(): ?int
    {
        return $this->hours;
    }

    /**
     * @param string $hours
     */
    public function setHours(int $hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return ItemSnapshot
     */
    public function getItemSnapshot(): ?ItemSnapshot
    {
        return $this->itemSnapshot;
    }

    /**
     * @param mixed $itemSnapshot
     */
    public function setItemSnapshot(ItemSnapshot $itemSnapshot): void
    {
        $this->itemSnapshot = $itemSnapshot;
    }


    public function getCost($vatIncluded = true)
    {
        if ($this->getPriceType() == Billable::BILLABLE_TYPES['SINGLE AMOUNT']) {
            return $this->getPrice($vatIncluded);
        } elseif ($this->getPriceType() == Billable::BILLABLE_TYPES['HOURLY AMOUNT']) {
            return $this->getPrice($vatIncluded) * $this->getHours();
        } else {
            throw new \Exception('Undefined cost for this price type');
        }

    }


    public function __toString()
    {

        return $this->itemSnapshot->getName() ?? '';
    }

}

