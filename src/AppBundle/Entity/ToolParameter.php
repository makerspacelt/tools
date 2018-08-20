<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tools_parameters")
 * @ORM\Entity
 */
class ToolParameter implements \Serializable {

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
    #=====================================================

    /**
     * String representation of object
     */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->name,
            $this->value
        ));
    }

    /**
     * Constructs the object
     */
    public function unserialize($serialized) {
        list(
            $this->id,
            $this->name,
            $this->value
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

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
    
}
