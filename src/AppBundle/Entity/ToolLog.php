<?php
/**
 * Created by PhpStorm.
 * User: Kulverstukas
 * Date: 2018-08-20
 * Time: 22:42
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tools_logs")
 * @ORM\Entity
 */
class ToolLog implements \Serializable {

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
    private $log;
    #=====================================================

    /**
     * String representation of object
     */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->log
        ));
    }

    /**
     * Constructs the object
     */
    public function unserialize($serialized) {
        list(
            $this->id,
            $this->log
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

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