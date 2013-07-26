<?php

namespace Tracker\CampaignBundle\Entity;

use Tracker\TargetBundle\Entity\Target;
use Tracker\CommonBundle\Validator\Constraints as TrackerAssert;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Tracker\CampaignBundle\Entity\Campaign
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tracker\CampaignBundle\Repository\CampaignRepository")
 * @Assert\Callback(methods={"isValid"})
 */
class Campaign
{
  const TYPE_DIRECT_LINK = 'direct_link';
  const TYPE_LANDING_PAGE = 'landing_page';
  const TYPE_BOTH = 'both';

  private static $xorKey = 0x40019;

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
   * @var text $notes
   *
   * @ORM\Column(name="notes", type="text", nullable=true)
   */
  private $notes;

  /**
   * @var string $type
   *
   * @ORM\Column(name="type", type="string")
   * @Assert\Choice(callback="getTypes")
   */
  private $type;

  /**
   * @var decimal $estimatedCostPerView
   *
   * @ORM\Column(name="estimatedCostPerView", type="decimal", scale="4")
   * @Assert\NotBlank
   * @Assert\Min(limit = "0", message = "This value must be non-negative")
   */
  private $estimatedCostPerView;

  /**
   * @var boolean $active
   *
   * @ORM\Column(name="active", type="boolean")
   */
  private $active = true;

  /**
   * @ORM\ManyToOne(targetEntity="Tracker\TrafficSourceBundle\Entity\TrafficSource", inversedBy="campaigns")
   * @ORM\JoinColumn(name="trafficSource_id", referencedColumnName="id")
   */
  private $trafficSource;

  /**
   * @var array $creativeIdentifiers;
   *
   * @ORM\Column(name="creativeIdentifiers", type="array", nullable=true)
   */
  private $creativeIdentifiers;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $landingPages
   *
   * @ORM\OneToMany(targetEntity="Tracker\CampaignBundle\Entity\LandingPage", mappedBy="campaign", cascade={"persist", "remove"})
   * @Assert\NotBlank
   */
  private $landingPages;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $offerGroups
   *
   * @ORM\OneToMany(targetEntity="Tracker\CampaignBundle\Entity\OfferGroup", mappedBy="campaign", cascade={"persist", "remove"})
   * @Assert\NotBlank
   * @Assert\All({
   *   @TrackerAssert\UniqueInCollection(propertyPath="name")
   * })
   */
  private $offerGroups;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $targets
   *
   * @ORM\OneToMany(targetEntity="Tracker\TargetBundle\Entity\Target", mappedBy="campaign", cascade={"persist", "remove"}, orphanRemoval=true)
   */
  private $targets;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $views
   *
   * @ORM\OneToMany(targetEntity="Tracker\TrackingBundle\Entity\View", mappedBy="campaign", cascade={"persist", "remove"}, orphanRemoval=true)
   */
  private $views;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->landingPages = new ArrayCollection();
    $this->offerGroups = new ArrayCollection();
    $this->targets = new ArrayCollection();
    $this->views = new ArrayCollection();
  }

  public function __toString()
  {
    return $this->getName();
  }

  public function __clone()
  {
    // If the entity has an identity, proceed as normal.
    // Note:  Doctrine requires this since it uses clone on entities that do not have ids set.
    if ($this->id)
    {
      $this->name .= ' Copy';

      // Clone the landing pages
      foreach ($this->getLandingpages() as $landingPage)
      {
        $clonedLandingPage = clone $landingPage;
        $this->addLandingPage($clonedLandingPage);
        $clonedLandingPage->setCampaign($this);
      }

      // Clone the offer groups
      foreach ($this->getOfferGroups() as $offerGroup)
      {
        $clonedOfferGroup = clone $offerGroup;
        $this->addOfferGroup($clonedOfferGroup);
        $clonedOfferGroup->setCampaign($this);
      }
    }
  }

  public function isValid(ExecutionContext $context)
  {
    // there must be at least one offer group
    if ($this->getOfferGroups()->count() == 0)
      $context->addViolation('There must be at least one offer group.', array(), null);

    // there must be at least one landing page if type is landing page or both
    if ($this->isTypeLandingPage() || $this->isTypeBoth())
    {
      if ($this->getLandingPages()->count() == 0)
        $context->addViolation('There must be at least one landing page.', array(), null);
    }

    // Sum of the landing page weights cannot be greater than 100
    if ($this->getLandingPages()->count() > 0)
    {
      $landingPageWeightSum = 0;
      foreach ($this->getLandingPages() as $landingPage)
        $landingPageWeightSum += $landingPage->getWeight();

      if ($landingPageWeightSum != 100)
        $context->addViolation('The landing page weights must add up to 100%.', array(), null);
    }
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
   * Gets a unique key for a campaign.
   *
   * @return string
   */
  public function getKey()
  {
    $n = $this->id ^ self::$xorKey;
    return gmp_strval(gmp_init($n, 10), 62);
  }

  /** 
   * Decodes |$key| into a unique id for a campaign.
   *
   * @return integer
   */
  public static function decodeKey($key)
  {
    return gmp_intval(gmp_init($key, 62)) ^ self::$xorKey;
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
   * Set type
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * Get type
   *
   * @return string 
   */
  public function getType()
  {
    return $this->type;
  }

  /** 
   * Get types
   *
   * @return array
   */
  public static function getTypes()
  {
    return array('Direct Link' => self::TYPE_DIRECT_LINK, 'Landing Page' => self::TYPE_LANDING_PAGE, 'Both' => self::TYPE_BOTH);
  }

  /**
   * Returns true if the campaign type is direct link
   */
  public function isTypeDirectLink()
  {
    return ($this->getType() == self::TYPE_DIRECT_LINK);
  }

  /**
   * Returns true if the campaign type is landing page
   */
  public function isTypeLandingPage()
  {
    return ($this->getType() == self::TYPE_LANDING_PAGE);
  }

  /**
   * Returns true if the campaign type is direct link and landing page
   */
  public function isTypeBoth()
  {
    return ($this->getType() == self::TYPE_BOTH);
  }

  /**
   * Set estimatedCostPerView
   *
   * @param decimal $estimatedCostPerView
   */
  public function setEstimatedCostPerView($estimatedCostPerView)
  {
    $this->estimatedCostPerView = $estimatedCostPerView;
  }

  /**
   * Get estimatedCostPerView
   *
   * @return decimal
   */
  public function getEstimatedCostPerView()
  {
    return $this->estimatedCostPerView;
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
   * Set trafficSource
   *
   * @param Tracker\TrafficSourceBundle\Entity\TrafficSource $trafficSource
   */
  public function setTrafficSource(\Tracker\TrafficSourceBundle\Entity\TrafficSource $trafficSource)
  {
    $this->trafficSource = $trafficSource;
  }

  /**
   * Get trafficSource
   *
   * @return Tracker\TrafficSourceBundle\Entity\TrafficSource 
   */
  public function getTrafficSource()
  {
    return $this->trafficSource;
  }

  /**
   * Set creativeIdentifiers
   *
   * @param array $creativeIdentifiers
   */
  public function setCreativeIdentifiers($creativeIdentifiers)
  {
    $this->creativeIdentifiers = $creativeIdentifiers;
  }

  /**
   * Get creativeIdentifiers
   *
   * @return array 
   */
  public function getCreativeIdentifiers()
  {
    return $this->creativeIdentifiers;
  }

  /**
   * Add landingPages
   *
   * @param LandingPage $landingPages
   */
  public function addLandingPage(\Tracker\CampaignBundle\Entity\LandingPage $landingPage)
  {
    $this->landingPages[] = $landingPage;
  }

  /**
   * Get landingPages
   *
   * @return Doctrine\Common\Collections\Collection
   */
  public function getLandingPages()
  {
    return $this->landingPages;
  }

  /**
   * Set landingPages
   *
   * @param Doctrine\Common\Collections\Collection
   */
  public function setLandingPages(\Doctrine\Common\Collections\Collection $landingPages)
  {
    foreach ($landingPages as $landingPage)
      $landingPage->setCampaign($this);

    $this->landingPages = $landingPages;
  }

  /**
   * Add offerGroups
   *
   * @param Tracker\CampaignBundle\Entity\OfferGroup $offerGroup
   */
  public function addOfferGroup(\Tracker\CampaignBundle\Entity\OfferGroup $offerGroup)
  {
    $this->offerGroups[] = $offerGroup;
  }

  /**
   * Get offerGroups
   *
   * @return Doctrine\Common\Collections\Collection 
   */
  public function getOfferGroups()
  {
    return $this->offerGroups;
  }

  /**
   * Add offerGroups
   *
   * @param Doctrine\Common\Collections\Collection $offerGroups
   */
  public function setOfferGroups(\Doctrine\Common\Collections\Collection $offerGroups)
  {
    foreach ($offerGroups as $offerGroup)
      $offerGroup->setCampaign($this);

    $this->offerGroups = $offerGroups;
  }

  /**
   * Add target
   *
   * @param Tracker\TargetBundle\Entity\Target $target
   */
  public function addTarget(\Tracker\TargetBundle\Entity\Target $target)
  {
    $this->targets[] = $target;
  }

  /**
   * Get targets
   *
   * @return Doctrine\Common\Collections\Collection
   */
  public function getTargets()
  {
    return $this->targets;
  }

  /**
   * Get the number of active targets.
   *
   * @return integer
   */
  public function getNumberOfActiveTargets()
  {
    return $this->getNumberOfTargetsWithStatus(true);
  }

  /**
   * Get the number of inactive targets.
   *
   * @return integer
   */
  public function getNumberOfInactiveTargets()
  {
    return $this->getNumberOfTargetsWithStatus(false);
  }

  /**
   * Get the number of targets with the given status.
   *
   * @return integer
   */
  private function getNumberOfTargetsWithStatus($active)
  {
    $count = 0;

    foreach ($this->getTargets() as $target)
    {
      if ($target->isActive() == $active)
        $count++;
    }

    return $count;
  }

  /**
   * Add view
   *
   * @param Tracker\TrackingBundle\Entity\View $view
   */
  public function addView(\Tracker\TrackingBundle\Entity\View $view)
  {
    $this->views[] = $view;
  }

  /**
   * Get views
   *
   * @return Doctrine\Common\Collections\Collection 
   */
  public function getViews()
  {
    return $this->views;
  }


  /**
   * Returns a random landing page, taking landing page weights into consideration
   */
  public function getRandomLandingPage()
  {
    // TODO: ignore landing pages that are inactive

    // Return the first landing page is there is only one
    if ($this->landingPages->count() == 1)
      return $this->landingPages[0];

    // Get the sum of the landing page weights
    $totalWeight = 0;
    foreach ($this->landingPages as $landingPage)
      $totalWeight += $landingPage->getWeight();

    // Get a random integer in [0, $totalWeight)
    $rand = rand(0, $totalWeight - 1);

    // Choose a landing page using the landing page weights
    $rangeMin = 0;
    foreach ($this->landingPages as $landingPage)
    {
      $rangeMax = $rangeMin + $landingPage->getWeight();

      // Use this landingPage if |$rangeMin <= $rand < $rangeMax|
      if ($rangeMin <= $rand && $rand < $rangeMax)
        break;

      $rangeMin = $rangeMax;
    }

    return $landingPage;
  }

  /**
   * Returns a destination (an offer or a landing page) that the user will be redirected to.
   */
  public function getDestination()
  {
    $offer = function($offerGroup) {
      return $offerGroup->getRandomOffer();
    };

    $landingPage = function($campaign) {
      return $campaign->getRandomLandingPage();
    };

    if ($this->isTypeDirectLink())
      return $offer($this->offerGroups[0]);
    else if ($this->isTypeLandingPage())
      return $landingPage($this);
    else if ($this->isTypeBoth())
    {
      // Return either an offer or a landing page (50/50 chance)
      return (rand(0, 1)) ? $offer($this->offerGroups[0]) : $landingPage($this);
    }
  }

  /**
   * Resets the stats by deleting all views, clicks, and conversions for this campaign.
   */
  public function resetStats()
  {
    // Clear the association with the views.  When the entity manager is flushed, the views will be removed since they're set with
    // orphanRemoval=true.  Removing the views will also cascade remove (at the ORM level) the related clicks and conversions.  Voila!
    $this->getViews()->clear();
  }
}
