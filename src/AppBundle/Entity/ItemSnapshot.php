<?php

namespace AppBundle\Entity;

use AppBundle\Model\Billable;
use AppBundle\Model\Item;
use AppBundle\Model\Priceable;
use AppBundle\Model\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Service;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\ServiceSnapshot;

/**
 * CostItem
 *
 * @ORM\Table(name="item_snapshot")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ItemSnapshotRepository")
 */
class ItemSnapshot implements Billable
{
    use Priceable, Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="integer", unique=true)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CostItem")
     * @ORM\JoinColumn(name="costItem_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $costItem;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceSnapshot", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="serviceSnapshot_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $serviceSnapshot;


    static function create(CostItem $costItem, int $version){

        $itemSnapshot = new ItemSnapshot($costItem, $version);

        $itemSnapshot->name = $costItem->getName();
        $itemSnapshot->setPrice($costItem->getPrice());
        $itemSnapshot->setPriceType($costItem->getPriceType());
        $itemSnapshot->setCurrency($costItem->getCurrency());
        $itemSnapshot->setVat($costItem->getVat());

        $service = $costItem->getService();
        $serviceSnapshot = $serviceManager->getCurrentSnapshot($service);
        $version = $service->getUpdatedAt()->getTimestamp();
        $itemSnapshot->serviceSnapshot = $serviceSnapshot;

        return $itemSnapshot;
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

    public function __construct(CostItem $costItem, int $version)
    {
        $this->costItem = $costItem;
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CostItem|null
     */
    public function getCostItem()
    {
        return $this->costItem;
    }

    /**
     * @return Service|null
     */
    public function getServiceSnapshot(): ?ServiceSnapshot
    {
        return $this->serviceSnapshot;
    }

}

