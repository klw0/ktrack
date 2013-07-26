<?php

namespace Tracker\TargetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tracker\TargetBundle\Entity\Target
 *
 * @ORM\Table(indexes={@ORM\Index(name="search_idx", columns={"name", "campaign_id"})})
 * @ORM\Entity(repositoryClass="Tracker\TargetBundle\Entity\TargetRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Target
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
   * @ORM\Column(name="name", type="string", length=255)
   * @Assert\NotBlank
   */
  private $name;

  /**
   * @var boolean $active
   *
   * @ORM\Column(name="active", type="boolean")
   * @Assert\NotBlank
   */
  private $active;

  /**
   * @var decimal $currentBid
   *
   * @ORM\Column(name="currentBid", type="decimal", scale=4)
   * @Assert\NotBlank
   */
  private $currentBid;

  /**
   * @ORM\ManyToOne(targetEntity="Tracker\CampaignBundle\Entity\Campaign", inversedBy="targets")
   * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
   */
  private $campaign;

  /**
   * @ORM\Column(name="creativeIdentifier", type="string", length=255)
   * @Assert\NotBlank
   */
  private $creativeIdentifier;

  /**
   * @var datetime $createdAt
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  /**
   * @var datetime $updatedAt
   *
   * @ORM\Column(name="updatedAt", type="datetime")
   */
  private $updatedAt;


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
   * Set active
   *
   * @param boolean $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }

  /**
   * Get active
   *
   * @return boolean 
   */
  public function getActive()
  {
    return $this->active;
  }

  /**
   * Get active
   *
   * @return boolean 
   */
  public function isActive()
  {
    return $this->active;
  }

  /**
   * Set currentBid
   *
   * @param decimal $currentBid
   */
  public function setCurrentBid($currentBid)
  {
    $this->currentBid = $currentBid;
  }

  /**
   * Get currentBid
   *
   * @return decimal 
   */
  public function getCurrentBid()
  {
    return $this->currentBid;
  }

  /**
   * Set campaign
   *
   * @param Tracker\CampaignBundle\Entity\Campaign $campaign
   */
  public function setCampaign(\Tracker\CampaignBundle\Entity\Campaign $campaign)
  {
    $this->campaign = $campaign;
  }

  /**
   * Get campaign
   *
   * @return Tracker\CampaignBundle\Entity\Campaign 
   */
  public function getCampaign()
  {
    return $this->campaign;
  }

  /**
   * Set the creative identifier
   *
   * @param string $creativeIdentifier
   */
  public function setCreativeIdentifier($creativeIdentifier)
  {
    $this->creativeIdentifier = $creativeIdentifier;
  }

  /**
   * Get the creative identifier
   *
   * @return string
   */
  public function getCreativeIdentifier()
  {
    return $this->creativeIdentifier;
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
    $this->updatedAt = $this->createdAt;
  }

  /**
   * Set updatedAt
   *
   * @param datetime $updatedAt
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;
  }

  /**
   * Get updatedAt
   *
   * @return datetime 
   */
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  /**
   * Set |updatedAt| before persisting the object to the database after updating
   *
   * @ORM\preUpdate
   */
  public function setUpdatedAtValue()
  {
    $this->updatedAt = new \DateTime();
  }
}
