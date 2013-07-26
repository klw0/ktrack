<?php

namespace Tracker\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Tracker\CampaignBundle\Entity\OfferGroup
 *
 * @ORM\Table()
 * @ORM\Entity
 * @Assert\Callback(methods={"isOfferGroupValid"})
 */
class OfferGroup
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
   * @ORM\ManyToOne(targetEntity="Tracker\CampaignBundle\Entity\Campaign", inversedBy="offerGroups")
   * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
   */
  private $campaign;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $offers
   *
   * @ORM\OneToMany(targetEntity="Tracker\CampaignBundle\Entity\Offer", mappedBy="offerGroup", cascade={"persist", "remove"})
   * @Assert\NotBlank
   */
  private $offers;


  /**
   * Constructor
   */
  public function __construct()
  {
    $this->offers = new ArrayCollection();
  }

  public function __clone()
  {
    // If the entity has an identity, proceed as normal.
    // Note:  Doctrine requires this since it uses clone on entities that do not have ids set.
    if ($this->id)
    {
      // Clone the offers
      foreach ($this->getOffers() as $offer)
      {
        $clonedOffer = clone $offer;
        $this->addOffer($clonedOffer);
        $clonedOffer->setOfferGroup($this);
      }
    }
  }

  /**
   * Custom validator for this object
   */
  public function isOfferGroupValid(ExecutionContext $context)
  {
    if ($this->getOffers()->count() == 0)
      $context->addViolation('There must be at least one offer.', array(), null);

    // Sum of the offer weights cannot be greater than 100
    $offerWeightSum = 0;
    foreach ($this->getOffers() as $offer)
      $offerWeightSum += $offer->getWeight();

    if ($offerWeightSum != 100)
      $context->addViolation('The offer weights must add up to 100%.', array(), null);
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
   * Add offers
   *
   * @param Tracker\CampaignBundle\Entity\Offer $offer
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

  /**
   * Set offers
   *
   * @param Doctrine\Common\Collections\Collection $offers
   */
  // TODO: change this back once form binding is fixed in Symfony for nested collections
  // public function setOffers(\Doctrine\Common\Collections\Collection $offers)
  public function setOffers($offers)
  {
    // hack
    $offerCollection = new ArrayCollection();

    foreach ($offers as $offer)
    {
      $offer->setOfferGroup($this);
      $offerCollection->add($offer);    // hack
    }

    // $this->offers = $offers;
    $this->offers = $offerCollection;   // hack
  }

  /**
   * Returns a random offer, taken offer weights into consideration
   */
  public function getRandomOffer()
  {
    // TODO: ignore offers that are inactive

    // Return the first offer is there is only one offer
    if ($this->offers->count() == 1)
      return $this->offers[0];

    // Get the sum of the offer weights
    $totalWeight = 0;
    foreach ($this->offers as $offer)
      $totalWeight += $offer->getWeight();

    // Get a random integer in [0, $totalWeight)
    $rand = rand(0, $totalWeight - 1);

    // Choose an offer using the offer weights
    $rangeMin = 0;
    foreach ($this->offers as $offer)
    {
      $rangeMax = $rangeMin + $offer->getWeight();

      // Use this offer if |$rangeMin <= $rand < $rangeMax|
      if ($rangeMin <= $rand && $rand < $rangeMax)
        break;

      $rangeMin = $rangeMax;
    }

    return $offer;
  }
}
