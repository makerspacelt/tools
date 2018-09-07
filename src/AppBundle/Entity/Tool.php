<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tools_tool")
 * @ORM\Entity
 */
class Tool {

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

    /**
     * @ORM\OneToMany(targetEntity="ToolLog", mappedBy="tool")
     */
    private $logs;

    #=====================================================
    public function __construct() {
        $this->logs = new ArrayCollection();
    }
    #=====================================================

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getShopLinks() {
        return $this->shopLinks;
    }

    /**
     * @return mixed
     */
    public function getOriginalPrice() {
        return $this->originalPrice;
    }

    /**
     * @return mixed
     */
    public function getAcquisitionDate() {
        return $this->acquisitionDate;
    }

    /**
     * @return mixed
     */
    public function getLogs() {
        return $this->logs;
    }

    #=====================================================

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model) {
        $this->model = $model;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @param mixed $shopLinks
     */
    public function setShopLinks($shopLinks) {
        $this->shopLinks = $shopLinks;
    }

    /**
     * @param mixed $originalPrice
     */
    public function setOriginalPrice($originalPrice) {
        $this->originalPrice = $originalPrice;
    }

    /**
     * @param mixed $acquisitionDate
     */
    public function setAcquisitionDate($acquisitionDate) {
        $this->acquisitionDate = $acquisitionDate;
    }

    /**
     * @param mixed $logEntry
     */
    public function addLog($logEntry) {
        $logEntry->setTool($this);
        $this->logs->add($logEntry);
    }

    #=====================================================
}

