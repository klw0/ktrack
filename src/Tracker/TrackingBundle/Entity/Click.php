<?php

namespace Tracker\TrackingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tracker\TrackingBundle\Entity\Click
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Tracker\TrackingBundle\Entity\ClickRepository")
 */
class Click
{
  private static $xorKey = 0x44871;

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var datetime $createdAt
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  /**
   * @ORM\ManyToOne(targetEntity="Tracker\TrackingBundle\Entity\View", inversedBy="clicks")
   * @ORM\JoinColumn(name="view_id", referencedColumnName="id")
   */
  private $view;

  /**
   * @ORM\ManyToOne(targetEntity="Tracker\CampaignBundle\Entity\Offer", inversedBy="clicks")
   * @ORM\JoinColumn(name="offer_id", referencedColumnName="id")
   */
  private $offer;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $conversions
   *
   * @ORM\OneToMany(targetEntity="Tracker\TrackingBundle\Entity\Conversion", mappedBy="click", cascade={"persist", "remove"}, orphanRemoval=true)
   */
  private $conversions;


  /**
   * Constructor
   */
  public function __construct()
  {
    $this->conversions = new ArrayCollection();
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
   * Gets a unique key for the click. This is used as the sub id.
   *
   * @return string
   */
  public function getKey()
  {
    $n = $this->id ^ self::$xorKey;
    return gmp_strval(gmp_init($n, 10), 62);
  }

  /** 
   * Decodes |$key| into a unique id for the click.
   *
   * @return integer
   */
  public static function decodeKey($key)
  {
    return gmp_intval(gmp_init($key, 62)) ^ self::$xorKey;
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
   * Set view
   *
   * @param Tracker\TrackingBundle\Entity\View $view
   */
  public function setView(\Tracker\TrackingBundle\Entity\View $view)
  {
    $this->view = $view;
  }

  /**
   * Get view
   *
   * @return Tracker\TrackingBundle\Entity\View 
   */
  public function getView()
  {
    return $this->view;
  }

  /**
   * Set offer
   *
   * @param Tracker\CampaignBundle\Entity\Offer $offer
   */
  public function setOffer(\Tracker\CampaignBundle\Entity\Offer $offer)
  {
    $this->offer = $offer;
  }

  /**
   * Get offer
   *
   * @return Tracker\CampaignBundle\Entity\Offer 
   */
  public function getOffer()
  {
    return $this->offer;
  }

  /**
   * Add conversion
   *
   * @param Tracker\TrackingBundle\Entity\Conversion $conversion
   */
  public function addConversion(\Tracker\TrackingBundle\Entity\Conversion $conversion)
  {
    $this->conversions[] = $conversion;
  }

  /**
   * Get conversions
   *
   * @return Doctrine\Common\Collections\Collection 
   */
  public function getConversions()
  {
    return $this->conversions;
  }
}
