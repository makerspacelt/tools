<?php
/**
 * Created by PhpStorm.
 * User: Kulverstukas
 * Date: 2018-08-20
 * Time: 22:42
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tools_logs")
 * @ORM\Entity
 */
class ToolLog {

    #=====================================================
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tool", inversedBy="logEntry")
     * @ORM\JoinColumn(name="tool_id", referencedColumnName="id")
     */
    private $toolId;

    /**
     * @ORM\Column
     */
    private $log;
    #=====================================================

    /**
     * @return mixed
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * @param mixed $log
     */
    public function setLog($log) {
        $this->log = $log;
    }


}