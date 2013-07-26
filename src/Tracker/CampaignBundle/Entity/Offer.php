<?php

namespace Tracker\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tracker\CampaignBundle\Entity\Offer
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Offer
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
   * @var text $description
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  private $description;

  /**
   * @var string $url
   *
   * @ORM\Column(name="url", type="string")
   * @Assert\NotBlank
   * @Assert\Url
   */
  private $url;

  /**
   * @var decimal $payout
   *
   * @ORM\Column(name="payout", type="decimal", scale="2")
   * @Assert\NotBlank
   * @Assert\Min(limit = "0", message = "This value must be non-negative")
   */
  private $payout;

  /**
   * @var integer $weight
   *
   * @ORM\Column(name="weight", type="integer")
   * @Assert\NotBlank
   * @Assert\Min(limit = "0")
   * @Assert\Max(limit = "100")
   */
  private $weight;

  /**
   * @ORM\ManyToOne(targetEntity="Tracker\AffiliateNetworkBundle\Entity\AffiliateNetwork", inversedBy="offers")
   * @ORM\JoinColumn(name="affiliateNetwork_id", referencedColumnName="id")
   */
  private $affiliateNetwork;

  /**
   * @ORM\ManyToOne(targetEntity="Tracker\CampaignBundle\Entity\OfferGroup", inversedBy="offers")
   * @ORM\JoinColumn(name="offerGroup_id", referencedColumnName="id")
   */
  private $offerGroup;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $clicks
   *
   * @ORM\OneToMany(targetEntity="Tracker\TrackingBundle\Entity\Click", mappedBy="offer", cascade={"persist", "remove"})
   */
  private $clicks;

  /**
   * @var boolean $wantsSubId
   *
   * @ORM\Column(name="wantsSubId", type="boolean")
   */
  private $wantsSubId = true;

  /**
   * @var boolean $wantsQueryString
   *
   * @ORM\Column(name="wantsQueryString", type="boolean")
   */
  private $wantsQueryString = true;


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
   * Set description
   *
   * @param text $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * Get description
   *
   * @return text 
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Set url
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }

  /**
   * Get url
   *
   * @return string 
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * Set payout
   *
   * @param decimal $payout
   */
  public function setPayout($payout)
  {
    $this->payout = $payout;
  }

  /**
   * Get payout
   *
   * @return decimal 
   */
  public function getPayout()
  {
    return $this->payout;
  }

  /**
   * Set weight
   *
   * @param integer $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }

  /**
   * Get weight
   *
   * @return integer 
   */
  public function getWeight()
  {
    return $this->weight;
  }

  /**
   * Set affiliateNetwork
   *
   * @param Tracker\AffiliateNetworkBundle\Entity\AffiliateNetwork $affiliateNetwork
   */
  public function setAffiliateNetwork(\Tracker\AffiliateNetworkBundle\Entity\AffiliateNetwork $affiliateNetwork)
  {
    $this->affiliateNetwork = $affiliateNetwork;
  }

  /**
   * Get affiliateNetwork
   *
   * @return Tracker\AffiliateNetworkBundle\Entity\AffiliateNetwork 
   */
  public function getAffiliateNetwork()
  {
    return $this->affiliateNetwork;
  }

  /**
   * Set offerGroup
   *
   * @param Tracker\CampaignBundle\Entity\OfferGroup $offerGroup
   */
  public function setOfferGroup(\Tracker\CampaignBundle\Entity\OfferGroup $offerGroup)
  {
    $this->offerGroup = $offerGroup;
  }

  /**
   * Get offerGroup
   *
   * @return Tracker\CampaignBundle\Entity\OfferGroup 
   */
  public function getOfferGroup()
  {
    return $this->offerGroup;
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

  /**
   * Sets if the offer wants the sub id passed to it's offer url.
   *
   * @param boolean $wantsSubId
   */
  public function setWantsSubId($wantsSubId)
  {
    $this->wantsSubId = $wantsSubId;
  }

  /**
   * See setWantsSubId().
   *
   * @return boolean 
   */
  public function getWantsSubId()
  {
    return $this->wantsSubId;
  }

  /**
   * Sets if the offer wants the query string from the click request to be passed to the offer url.
   *
   * @param boolean $wantsQueryString
   */
  public function setWantsQueryString($wantsQueryString)
  {
    $this->wantsQueryString = $wantsQueryString;
  }

  /**
   * See setWantsQueryString().
   *
   * @return boolean 
   */
  public function getWantsQueryString()
  {
    return $this->wantsQueryString;
  }

  /**
   * Returns the offer's URL complete with query string parameters.
   *
   * @return string
   */
  public function getRedirectUrl($subId, $queryString = null)
  {
    $url = $this->getUrl();

    // Should we append the sub id?
    if ($this->getWantsSubId())
      $url .= $subId;

    // Should we append the query string?
    if ($this->getWantsQueryString() && $queryString !== null)
    {
      // If there is no existing query string, there will not be a '?' in the URL
      $url .= (strpos($url, '?') === false) ? '?' : '&';
      $url .= $queryString;
    }

    return $url;
  }
}
