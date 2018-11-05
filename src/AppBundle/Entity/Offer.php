<?php

namespace AppBundle\Entity;

use AppBundle\Model\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Service;

/**
 * Offer
 *
 * @ORM\Table(name="offer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfferRepository")
 */
class Offer
{
    use Timestampable;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceSnapshot", cascade={"persist"})
     * @ORM\JoinColumn(name="snapshot_id", referencedColumnName="id", nullable=false)
     */
    protected $serviceSnapshot;

    /**
     * @var OfferItem[] $items
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OfferItem", mappedBy="offer", cascade={"persist", "remove"})
     */
    protected $items;

    public function __construct(ServiceSnapshot $serviceSnapshot, $items) {

        $this->serviceSnapshot = $serviceSnapshot;
        $this->setItems($items);

        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ServiceSnapshot
     */
    public function getServiceSnapshot(): ?ServiceSnapshot
    {
        return $this->serviceSnapshot;
    }

    /**
     * @param ServiceSnapshot $serviceSnapshot
     */
    public function setServiceSnapshot(ServiceSnapshot $serviceSnapshot): void
    {
        $this->serviceSnapshot = $serviceSnapshot;
    }

    /**
     * @return OfferItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items): void
    {

        $this->items = new ArrayCollection();

        foreach ($items as $item){
            $this->items->add($item);
            $item->setOffer($this);
        }
    }




}
