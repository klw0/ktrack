<?php

namespace Tracker\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tracker\CampaignBundle\Entity\LandingPage
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LandingPage
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
   * @var string $url
   *
   * @ORM\Column(name="url", type="string")
   * @Assert\NotBlank
   * @Assert\Url
   */
  private $url;

  /**
   * @var string $viewKeyParameter
   *
   * @ORM\Column(name="viewKeyParameter", type="string", length=16)
   * @Assert\NotBlank
   */
  private $viewKeyParameter = 'v';

  /**
   * @var string $wantsTarget
   *
   * @ORM\Column(name="wantsTarget", type="boolean")
   */
  private $wantsTarget = true;

  /**
   * @var string $targetParameter
   *
   * @ORM\Column(name="targetParameter", type="string", length=16)
   * @Assert\NotBlank
   */
  private $targetParameter = 'target';

  /**
   * @var text $description
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  private $description;

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
   * @ORM\ManyToOne(targetEntity="Tracker\CampaignBundle\Entity\Campaign", inversedBy="landingPages")
   * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
   */
  private $campaign;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $views
   *
   * @ORM\OneToMany(targetEntity="Tracker\TrackingBundle\Entity\View", mappedBy="landingPage", cascade={"persist", "remove"})
   */
  private $views;


  /**
   * Constructor
   */
  public function __construct()
  {
    $this->views = new ArrayCollection();
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
   * Set the view key parameter.
   *
   * @param string $parameter
   */
  public function setViewKeyParameter($parameter)
  {
    $this->viewKeyParameter = $parameter;
  }

  /**
   * Get the view key parameter.
   *
   * @return string
   */
  public function getViewKeyParameter()
  {
    return $this->viewKeyParameter;
  }

  /**
   * Set if the landing page wants the target to be passed to it.
   *
   * @param boolean $wantsTarget
   */
  public function setWantsTarget($wantsTarget)
  {
    $this->wantsTarget = $wantsTarget;
  }

  /**
   * See setWantsTarget().
   *
   * @return boolean
   */
  public function getWantsTarget()
  {
    return $this->wantsTarget;
  }

  /**
   * Set the query string parameter to be used when the target is passed to the landing page.
   *
   * @param string $parameter
   */
  public function setTargetParameter($parameter)
  {
    $this->targetParameter = $parameter;
  }

  /**
   * See setTargetParameter().
   *
   * @return string
   */
  public function getTargetParameter()
  {
    return $this->targetParameter;
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
   * Set campaign
   *
   * @param Campaign $campaign
   */
  public function setCampaign(\Tracker\CampaignBundle\Entity\Campaign $campaign)
  {
    $this->campaign = $campaign;
  }

  /**
   * Get campaign
   *
   * @return Tracker\CampaignBundle\Eneity\Campaign
   */
  public function getCampaign()
  {
    return $this->campaign;
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
   * Returns the redirect URL complete with query string parameters.
   *
   * @return string
   */
  public function getRedirectUrl($viewKey, $target)
  {
    $url = $this->getUrl();

    // If there is no existing query string, there will not be a '?' in the URL
    $url .= (strpos($url, '?') === false) ? '?' : '&';

    // Append the view key
    $url .= $this->getViewKeyParameter() . '=' . $viewKey;

    // Should we append the target as a query string parameter?
    if ($this->getWantsTarget())
      $url .= '&' . $this->getTargetParameter() . '=' . $target;

    return $url;
  }
}
