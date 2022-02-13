<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="tools_tool")
 * @ORM\Entity(repositoryClass="App\Repository\ToolsRepository")
 */
class Tool
{
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
     * @Assert\NotNull(
     *     message = "Code cannot be empty"
     * )
     * @Assert\Regex(
     *     pattern = "/^\d{6}$/",
     *     message = "Code must be 6 numbers long"
     * )
     */
    private $code;

    /**
     * @ORM\Column(nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="shop_links", nullable=true)
     */
    private $shopLinks;

    /**
     * @ORM\Column(name="original_price", nullable=true)
     * @Assert\Regex(
     *     pattern = "/^\d+([,\.]\d+)?$/",
     *     match = "true",
     *     message = "Only valid price format is permitted"
     * )
     */
    private $originalPrice;

    /**
     * @ORM\Column(name="acquisition_date", nullable=true, type="date")
     */
    private $acquisitionDate;

    /**
     * @ORM\Column(name="instructions_pdf", nullable=true)
     */
    private $instructionsPdf;

    /**
     * @ORM\ManyToMany(targetEntity="ToolTag", mappedBy="tool", cascade={"persist"})
     */
    public $tags;

    /**
     * @ORM\OneToMany(targetEntity="ToolLog", mappedBy="tool", cascade={"all"}, orphanRemoval=true)
     */
    public $logs;

    /**
     * @ORM\OneToMany(targetEntity="ToolParameter", mappedBy="tool", cascade={"all"}, orphanRemoval=true)
     */
    public $params;

    /**
     * @ORM\OneToMany(targetEntity="ToolPhoto", mappedBy="tool", cascade={"all"}, orphanRemoval=true)
     */
    public $photos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    #=====================================================
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->params = new ArrayCollection();
        $this->photos = new ArrayCollection();
    }
    #=====================================================

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getShopLinks($parse = false)
    {
        if ($parse) {
            return preg_replace('#(?xi)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`\!()\[\]{};:\'".,<>?«»“”‘’]))#i',
                '<a href="$0" target="_blank">$0</a>', trim($this->shopLinks));
        } else {
            return $this->shopLinks;
        }
    }

    /**
     * @return mixed
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * @return mixed
     */
    public function getAcquisitionDate()
    {
        return $this->acquisitionDate;
    }

    public function getAcquisitionDateString()
    {
        return $this->acquisitionDate->format('Y-m-d');
    }

    public function getInstructionsPdf()
    {
        return $this->instructionsPdf;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return array
     */
    public function getTagsArray()
    {
        $tagsArr = [];
        foreach ($this->tags as $tag) {
            $tagsArr[] = $tag->getTag();
        }
        return $tagsArr;
    }

    /**
     * @return mixed
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    #=====================================================

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param mixed $shopLinks
     */
    public function setShopLinks($shopLinks)
    {
        $this->shopLinks = $shopLinks;
    }

    /**
     * @param mixed $originalPrice
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;
    }

    /**
     * @param mixed $acquisitionDate
     */
    public function setAcquisitionDate($acquisitionDate)
    {
        $this->acquisitionDate = $acquisitionDate;
    }

    public function setInstructionsPdf($instructionsPdf)
    {
        $this->instructionsPdf = $instructionsPdf;
    }

    /**
     * @param mixed $tags
     */
    public function addTag(ToolTag $tagEntry)
    {
        $tagEntry->addTool($this);
        $this->tags->add($tagEntry);
    }

    /**
     * @param mixed $logEntry
     */
    public function addLog(ToolLog $logEntry)
    {
        $logEntry->setTool($this);
        $this->logs->add($logEntry);
    }

    public function removeLog(ToolLog $logEntry)
    {
        $this->logs->removeElement($logEntry);
        $logEntry->setTool(null);
    }

    /**
     * @param mixed $params
     */
    public function addParam(ToolParameter $paramEntry)
    {
        $paramEntry->setTool($this);
        $this->params->add($paramEntry);
    }

    public function removeParam(ToolParameter $paramEntry)
    {
        $this->params->removeElement($paramEntry);
        $paramEntry->setTool(null);
    }

    /**
     * @param mixed $photos
     */
    public function addPhoto(ToolPhoto $photoEntry)
    {
        $photoEntry->setTool($this);
        $this->photos->add($photoEntry);
    }

    public function removePhoto(ToolPhoto $photoEntry)
    {
        $this->photos->removeElement($photoEntry);
        $photoEntry->removeTool();
    }
    #=====================================================

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status)
    {
        $this->status = $status;
    }

    public function getStatusIcon() : ?string
    {
        switch ($this->getStatus()) {
            case 'LOG_TYPE_BROKEN':
                return "fas fa-times";
            case 'LOG_TYPE_FIXED':
                return "fas fa-check";
            case 'LOG_TYPE_INPROGRESS':
                return "fas fa-spinner";
            default:
                return "";
                break;
        }
    }
}

