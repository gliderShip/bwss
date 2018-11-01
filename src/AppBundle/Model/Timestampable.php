<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:28 PM
 */

namespace AppBundle\Model;

use Doctrine\ORM\Mapping as ORM;


trait Timestampable
{
    /**
     * @ORM\Column(type="datetime")
     * @var $createdAt \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @var $updatedAt \DateTime
     */
    protected $updatedAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

}