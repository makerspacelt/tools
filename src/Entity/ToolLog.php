<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tools_logs")
 * @ORM\Entity
 */
class ToolLog
{

    #=====================================================
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tool", inversedBy="logs")
     */
    private $tool;

    /**
     * @ORM\Column
     */
    private $log;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;
    #=====================================================

    /**
     * @return mixed
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param mixed $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @param Tool $tool
     */
    public function setTool($tool)
    {
        $this->tool = $tool;
    }

    public function removeTool()
    {
        $this->tool = null;
    }

    /**
     * return Tool
     */
    public function getTool()
    {
        return $this->tool;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }
}

