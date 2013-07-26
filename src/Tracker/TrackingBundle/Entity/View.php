<?php

namespace Tracker\TrackingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tracker\TrackingBundle\Entity\View
 *
 * @ORM\Table(indexes={@ORM\Index(name="view_createdat_idx", columns={"createdAt"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class View
{
  private static $xorKey = 0x45101;

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string $targetName
   *
   * @ORM\Column(name="targetName", type="string", length=255)
   */
  private $targetName;

  /**
   * @var decimal $cost
   *
   * @ORM\Column(name="cost", type="decimal", scale=4)
   */
  private $cost;

  /**
   * @var string $ip
   *
   * @ORM\Column(name="ip", type="string", length=64)
   */
  private $ip;

  /**
   * @var string $extraData1
   *
   * @ORM\Column(name="extraData1", type="string", length=128, nullable=true)
   */
  private $extraData1;

  /**
   * @var string $extraData2
   *
   * @ORM\Column(name="extraData2", type="string", length=128, nullable=true)
   */
  private $extraData2;

  /**
   * @var string $extraData3
   *
   * @ORM\Column(name="extraData3", type="string", length=128, nullable=true)
   */
  private $extraData3;

  /**
   * @var string $extraData4
   *
   * @ORM\Column(name="extraData4", type="string", length=128, nullable=true)
   */
  private $extraData4;

  /**
   * @var string $extraData5
   *
   * @ORM\Column(name="extraData5", type="string", length=128, nullable=true)
   */
  private $extraData5;

  /**
   * @var datetime $createdAt
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  /**
   * @ORM\ManyToOne(targetEntity="Tracker\CampaignBundle\Entity\Campaign", inversedBy="views")
   * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
   */
  private $campaign;

  /**
   * @ORM\ManyToOne(targetEntity="Tracker\CampaignBundle\Entity\LandingPage", inversedBy="views")
   * @ORM\JoinColumn(name="landingPage_id", referencedColumnName="id")
   */
  private $landingPage;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $clicks
   *
   * @ORM\OneToMany(targetEntity="Tracker\TrackingBundle\Entity\Click", mappedBy="view", cascade={"persist", "remove"})
   */
  private $clicks;


  /**
   * Constructor
   */
  public function __construct()
  {
    $this->clicks = new ArrayCollection();
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
   * Gets a unique key for the view.
   *
   * @return string
   */
  public function getKey()
  {
    $n = $this->id ^ self::$xorKey;
    return gmp_strval(gmp_init($n, 10), 62);
  }

  /** 
   * Decodes |$key| into a unique id for the view.
   *
   * @return integer
   */
  public static function decodeKey($key)
  {
    return gmp_intval(gmp_init($key, 62)) ^ self::$xorKey;
  }

  /**
   * Set targetName
   *
   * @param string $targetName
   */
  public function setTargetName($targetName)
  {
    $this->targetName = $targetName;
  }

  /**
   * Get targetName
   *
   * @return string 
   */
  public function getTargetName()
  {
    return $this->targetName;
  }

  /**
   * Set cost
   *
   * @param decimal $cost
   */
  public function setCost($cost)
  {
    $this->cost = $cost;
  }

  /**
   * Get cost
   *
   * @return decimal 
   */
  public function getCost()
  {
    return $this->cost;
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

  /**
   * Sets extraData1
   *
   * @param string $data
   */
  public function setExtraData1($data)
  {
    $this->extraData1 = $data;
  }

  /**
   * Gets extraData1
   *
   * @return string
   */
  public function getExtraData1()
  {
    return $this->extraData1;
  }

  /**
   * Sets extraData2
   *
   * @param string $data
   */
  public function setExtraData2($data)
  {
    $this->extraData2 = $data;
  }

  /**
   * Gets extraData2
   *
   * @return string
   */
  public function getExtraData2()
  {
    return $this->extraData2;
  }

  /**
   * Sets extraData3
   *
   * @param string $data
   */
  public function setExtraData3($data)
  {
    $this->extraData3 = $data;
  }

  /**
   * Gets extraData3
   *
   * @return string
   */
  public function getExtraData3()
  {
    return $this->extraData3;
  }

  /**
   * Sets extraData4
   *
   * @param string $data
   */
  public function setExtraData4($data)
  {
    $this->extraData4 = $data;
  }

  /**
   * Gets extraData4
   *
   * @return string
   */
  public function getExtraData4()
  {
    return $this->extraData4;
  }
 
  /**
   * Sets extraData5
   *
   * @param string $data
   */
  public function setExtraData5($data)
  {
    $this->extraData5 = $data;
  }

  /**
   * Gets extraData5
   *
   * @return string
   */
  public function getExtraData5()
  {
    return $this->extraData5;
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
   * Set landingPage
   *
   * @param Tracker\CampaignBundle\Entity\LandingPage $landingPage
   */
  public function setLandingPage(\Tracker\CampaignBundle\Entity\LandingPage $landingPage)
  {
    $this->landingPage = $landingPage;
  }

  /**
   * Get landingPage
   *
   * @return Tracker\CampaignBundle\Entity\LandingPage
   */
  public function getLandingPage()
  {
    return $this->landingPage;
  }

  /**
   * Add click
   *
   * @param Tracker\TrackingBundle\Entity\Click $click
   */
  public function addClick(\Tracker\TrackingBundle\Entity\Click $click)
  {
    $this->clicks[] = $click;
  }

  /**
   * Get clicks
   *
   * @return Doctrine\Common\Collections\Collection 
   */
  public function getClicks()
  {
    return $this->clicks;
  }
}
