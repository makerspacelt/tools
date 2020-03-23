<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tools_photos")
 * @ORM\Entity
 */
class ToolPhotos
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
    private $fileName;

    /**
     * @ORM\ManyToOne(targetEntity="Tool", inversedBy="photos")
     */
    private $tool;

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return mixed
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * @param mixed $tool
     */
    public function removeTool()
    {
        $this->tool = null;
    }

    /**
     * @param mixed $tool
     */
    public function setTool($tool)
    {
        $this->tool = $tool;
    }

}
