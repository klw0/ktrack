<?php

namespace Tracker\TrackingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tracker\TrackingBundle\Entity\Conversion
 *
 * @ORM\Table(indexes={@ORM\Index(name="conversion_createdat_idx", columns={"createdAt"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Conversion
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
   * @var decimal $revenue
   *
   * @ORM\Column(name="revenue", type="decimal", scale=4)
   */
  private $revenue;

  /**
   * @var datetime $createdAt
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  /**
   * @ORM\ManyToOne(targetEntity="Tracker\TrackingBundle\Entity\Click", inversedBy="conversions")
   * @ORM\JoinColumn(name="click_id", referencedColumnName="id")
   */
  private $click;


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
   * Set revenue
   *
   * @param decimal $revenue
   */
  public function setRevenue($revenue)
  {
    $this->revenue = $revenue;
  }

  /**
   * Get revenue
   *
   * @return decimal 
   */
  public function getRevenue()
  {
    return $this->revenue;
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
   * Set click
   *
   * @param Tracker\TrackingBundle\Entity\Click $click
   */
  public function setClick(\Tracker\TrackingBundle\Entity\Click $click)
  {
    $this->click = $click;
  }

  /**
   * Get click
   *
   * @return Tracker\TrackingBundle\Entity\Click 
   */
  public function getClick()
  {
    return $this->click;
  }
}
