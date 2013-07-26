<?php

namespace Tracker\AffiliateNetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tracker\AffiliateNetworkBundle\Entity\AffiliateNetwork
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class AffiliateNetwork
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
   * @var string $shortName
   *
   * @ORM\Column(name="shortName", type="string", length=255, nullable=true)
   */
  private $shortName;

  /**
   * @ORM\OneToOne(targetEntity="Tracker\AccountManagerBundle\Entity\AccountManager", cascade={"persist", "remove"})
   * @Assert\Type(type="Tracker\AccountManagerBundle\Entity\AccountManager")
   */
  private $accountManager;

  /**
   * @ORM\Column(name="siteUrl", type="string", length=255)
   * @Assert\NotBlank
   * @Assert\Url
   */
  private $siteUrl;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $offers
   *
   * @ORM\OneToMany(targetEntity="Tracker\CampaignBundle\Entity\Offer", mappedBy="affiliateNetwork")
   */
  private $offers;


  /**
   * Constructor
   */
  public function __construct()
  {
    $this->offers = new ArrayCollection();
  }

  /**
   * Returns a string representation
   */
  public function __toString()
  {
    return $this->getName();
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
   * Set shortName
   *
   * @param string $shortName
   */
  public function setShortName($shortName)
  {
    $this->shortName = $shortName;
  }

  /**
   * Get shortName
   *
   * @return string 
   */
  public function getShortName()
  {
    return $this->shortName;
  }

  /**
   * Set accountManager
   *
   * @param Tracker\AccountManagerBundle\Entity\AccountManager $accountManager
   */
  public function setAccountManager(\Tracker\AccountManagerBundle\Entity\AccountManager $accountManager)
  {
    $this->accountManager = $accountManager;
  }

  /**
   * Get accountManager
   *
   * @return Tracker\AccountManagerBundle\Entity\AccountManager 
   */
  public function getAccountManager()
  {
    return $this->accountManager;
  }

  /**
   * Set siteUrl
   *
   * @param string $siteUrl
   */
  public function setSiteUrl($siteUrl)
  {
    $this->siteUrl = $siteUrl;
  }

  /**
   * Get siteUrl
   *
   * @return string 
   */
  public function getSiteUrl()
  {
    return $this->siteUrl;
  }

  /**
   * Add offers
   *
   * @param Tracker\CampaignBundle\Entity\Offer $offers
   */
  public function addOffer(\Tracker\CampaignBundle\Entity\Offer $offer)
  {
    $this->offers[] = $offer;
  }

  /**
   * Get offers
   *
   * @return Doctrine\Common\Collections\Collection 
   */
  public function getOffers()
  {
    return $this->offers;
  }
}
