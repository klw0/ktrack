<?php

namespace Tracker\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tracker\SecurityBundle\Entity\LoginAttempt
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class LoginAttempt
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
   * @var string $username
   *
   * @ORM\Column(name="username", type="string", length=255)
   */
  private $username;

  /**
   * @var boolean $successful
   *
   * @ORM\Column(name="successful", type="boolean")
   */
  private $successful;

  /**
   * @var string $ip
   *
   * @ORM\Column(name="ip", type="string", length=64)
   */
  private $ip;

  /**
   * @var string $userAgent
   *
   * @ORM\Column(name="userAgent", type="string")
   */
  private $userAgent;

  /**
   * @var datetime $createdAt
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

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
   * Set username
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }

  /**
   * Get username
   *
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * Set successful
   *
   * @param boolean $successful
   */
  public function setSuccessful($successful)
  {
    $this->successful = $successful;
  }

  /**
   * Get successful
   *
   * @return boolean
   */
  public function getSuccessful()
  {
    return $this->successful;
  }

  /**
   * Set IP
   *
   * @param string $ip
   */
  public function setIp($ip)
  {
    $this->ip = $ip;
  }

  /**
   * Get IP
   *
   * @return string
   */
  public function getIp()
  {
    return $this->ip;
  }

  /**
   * Set createdAt
   *
   * @param datetime $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }

  /**
   * Get createdAt
   *
   * @return datetime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * Set |createdAt| before persisting the object to the database
   *
   * @ORM\prePersist
   */
  public function setCreatedAtValue()
  {
    $this->createdAt = new \DateTime();
  }

  /**
   * Set userAgent
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }

  /**
   * Get userAgent
   *
   * @return string 
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
}
