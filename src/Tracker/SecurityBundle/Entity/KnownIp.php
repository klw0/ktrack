<?php

namespace Tracker\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tracker\SecurityBundle\Entity\KnownIp
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class KnownIp
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $ip
     *
     * @ORM\Column(name="ip", type="string", length=64)
     */
    private $ip;

    /**
     * String representation of this object
     */
    public function __toString()
    {
      return $this->getIp();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ip
     *
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }
}
