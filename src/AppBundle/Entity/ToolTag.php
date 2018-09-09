<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tools_tags")
 * @ORM\Entity
 */
class ToolTag {

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
    private $tag;

    /**
     * @ORM\ManyToMany(targetEntity="Tool", inversedBy="tags")
     */
    private $tool;
    #=====================================================
    public function __construct() {
        $this->tool = new ArrayCollection();
    }
    #=====================================================

    /**
     * @return mixed
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag) {
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getTool() {
        return $this->tool;
    }

    /**
     * @param mixed $tool
     */
    public function setTool(Tool $tool) {
        $this->tool->add($tool);
    }

}