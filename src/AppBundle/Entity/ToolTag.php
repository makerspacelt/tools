<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="tools_tags")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagsRepository")
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
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     match=true,
     *     message="Tag name can only contain letters"
     * )
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
    public function getId() {
        return $this->id;
    }

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
    public function getTools() {
        return $this->tool;
    }

    /**
     * @param mixed $tool
     */
    public function setTool(Tool $tool) {
        $this->tool->add($tool);
    }

    public function countTools() {
        return $this->tool->count();
    }
}