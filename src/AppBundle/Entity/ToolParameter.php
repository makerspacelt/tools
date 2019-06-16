<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tools_parameters")
 * @ORM\Entity
 */
class ToolParameter {

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
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Tool", inversedBy="params")
     */
    private $tool;
    #=====================================================

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) {
        $this->value = $value;
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
    public function setTool($tool) {
        $this->tool = $tool;
    }

    public function removeTool() {
        $this->tool = null;
    }

}
