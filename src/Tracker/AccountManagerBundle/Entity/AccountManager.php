<?php

namespace Tracker\AccountManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tracker\AccountManagerBundle\Entity\AccountManager
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class AccountManager
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
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=255, nullable=true)
   */
  private $name;

  /**
   * @var string $email
   *
   * @ORM\Column(name="emailAddress", type="string", length=255, nullable=true)
   * @Assert\Email
   */
  private $email;

  /**
   * @var string $phone
   *
   * @ORM\Column(name="phone", type="string", length=255, nullable=true)
   */
  private $phone;

  /**
   * @var text $notes
   *
   * @ORM\Column(name="notes", type="text", nullable=true)
   */
  private $notes;


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
   * Set name
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * Get name
   *
   * @return string 
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set emailAddress
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }

  /**
   * Get emailAddress
   *
   * @return string 
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }

  /**
   * Set phone
   *
   * @param string $phone
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;
  }

  /**
   * Get phone
   *
   * @return string 
   */
  public function getPhone()
  {
    return $this->phone;
  }

  /**
   * Set notes
   *
   * @param text $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }

  /**
   * Get notes
   *
   * @return text 
   */
  public function getNotes()
  {
    return $this->notes;
  }

  /**
   * Set email
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

  /**
   * Get email
   *
   * @return string 
   */
  public function getEmail()
  {
    return $this->email;
  }
}