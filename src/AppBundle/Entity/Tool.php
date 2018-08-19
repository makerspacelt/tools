<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tools_tool")
 * @ORM\Entity
 */
class Tool implements \Serializable {

    #=====================================================
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column
     */
    private $name;

    /**
     * @ORM\Column
     */
    private $model;

    /**
     * @ORM\Column
     */
    private $code;

    /**
     * @ORM\Column
     */
    private $description;

    /**
     * @ORM\Column(name="shop_links")
     */
    private $shopLinks;

    /**
     * @ORM\Column(name="original_price")
     */
    private $originalPrice;

    /**
     * @ORM\Column(name="acquisition_date")
     */
    private $acquisitionDate;
    #=====================================================

    /**
     * String representation of object
     */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->name,
            $this->model,
            $this->code,
            $this->description,
            $this->shopLinks,
            $this->originalPrice,
            $this->acquisitionDate
        ));
    }

    /**
     * Constructs the object
     */
    public function unserialize($serialized) {
        list(
            $this->id,
            $this->name,
            $this->model,
            $this->code,
            $this->description,
            $this->shopLinks,
            $this->originalPrice,
            $this->acquisitionDate
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

}