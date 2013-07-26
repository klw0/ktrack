<?php

namespace Tracker\TrafficSourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tracker\TrafficSourceBundle\Entity\TrafficSource
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TrafficSource
{
  const SERVICE_AUTHENTICATION_METHOD_API_KEY = 'api_key';
  const SERVICE_AUTHENTICATION_METHOD_USERNAME_PASSWORD = 'username_password';

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
   * @ORM\OneToOne(targetEntity="Tracker\AccountManagerBundle\Entity\AccountManager", cascade={"persist", "remove"})
   * @Assert\Type(type="Tracker\AccountManagerBundle\Entity\AccountManager")
   */
  private $accountManager;

  /**
   * @ORM\Column(name="siteUrl", type="string", length=255, nullable=true)
   * @Assert\Url
   */
  private $siteUrl;

  /**
   * @ORM\Column(name="targetParameter", type="string", length=255)
   * @Assert\NotBlank
   */
  private $targetParameter;

  /**
   * @ORM\Column(name="targetToken", type="string", length=255)
   * @Assert\NotBlank
   */
  private $targetToken;

  /**
   * @ORM\Column(name="serviceName", type="string", length=128, nullable=true)
   */
  private $serviceName;

  /**
   * @var string $serviceAuthenticationMethod;
   *
   * @ORM\Column(name="serviceAuthenticationMethod", type="string")
   * @Assert\Choice(callback="getServiceAuthenticationMethods")
   */
  private $serviceAuthenticationMethod = self::SERVICE_AUTHENTICATION_METHOD_USERNAME_PASSWORD;

  /**
   * @ORM\Column(name="serviceApiKey", type="string", nullable=true)
   */
  private $serviceApiKey;

  /**
   * @ORM\Column(name="serviceUsername", type="string", nullable=true)
   */
  private $serviceUsername;

  /**
   * @ORM\Column(name="servicePassword", type="string", nullable=true)
   */
  private $servicePassword;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection $campaigns
   *
   * @ORM\OneToMany(targetEntity="Tracker\CampaignBundle\Entity\Campaign", mappedBy="trafficSource")
   */
  private $campaigns;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->campaigns = new ArrayCollection();
  }

  /**
   * Human readable magic
   */
  public function __toString()
  {
    return $this->name;
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
   * Set targetParameter
   *
   * @param string $targetParameter
   */
  public function setTargetParameter($targetParameter)
  {
    $this->targetParameter = $targetParameter;
  }

  /**
   * Get targetParameter
   *
   * @return string 
   */
  public function getTargetParameter()
  {
    return $this->targetParameter;
  }

  /**
   * Set targetToken
   *
   * @param string $targetToken
   */
  public function setTargetToken($targetToken)
  {
    $this->targetToken = $targetToken;
  }

  /**
   * Get targetToken
   *
   * @return string 
   */
  public function getTargetToken()
  {
    return $this->targetToken;
  }

  /**
   * Add campaign
   *
   * @param Tracker\CampaignBundle\Entity\Campaign $campaign
   */
  public function addCampaign(\Tracker\CampaignBundle\Entity\Campaign $campaign)
  {
    $this->campaigns[] = $campaign;
  }

  /**
   * Get campaigns
   *
   * @return Doctrine\Common\Collections\Collection 
   */
  public function getCampaigns()
  {
    return $this->campaigns;
  }

  /**
   * Set serviceName
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }

  /**
   * Get serviceName
   *
   * @return string 
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }

  /**
   * Returns if the entity has a service to get data from the traffic source.
   *
   * @return boolean 
   */
  public function hasService()
  {
    return ($this->getServiceName() !== null);
  }

  /**
   * Set service authentication method
   *
   * @param string $method
   */
  public function setServiceAuthenticationMethod($method)
  {
    $this->serviceAuthenticationMethod = $method;
  }

  /**
   * Get the service authentication method
   *
   * @return string
   */
  public function getServiceAuthenticationMethod()
  {
    return $this->serviceAuthenticationMethod;
  }

  /**
   * Get the possible service authentication methods
   *
   * @return array
   */
  public static function getServiceAuthenticationMethods()
  {
    return array(
      'API Key' => self::SERVICE_AUTHENTICATION_METHOD_API_KEY,
      'Username/Password' => self::SERVICE_AUTHENTICATION_METHOD_USERNAME_PASSWORD,
    );
  }

  /**
   * Is the authentication method API key?
   */
  public function serviceAuthenticatesWithApiKey()
  {
    return ($this->getServiceAuthenticationMethod() == self::SERVICE_AUTHENTICATION_METHOD_API_KEY);
  }

  /**
   * Set the service's API key
   */
  public function setServiceApiKey($key)
  {
    $this->serviceApiKey = $key;
  }

  /**
   * Get the service's API key
   */
  public function getServiceApiKey()
  {
    return $this->serviceApiKey;
  } 

  /**
   * Set the service's username
   */
  public function setServiceUsername($username)
  {
    $this->serviceUsername = $username;
  }

  /**
   * Get the service's username
   */
  public function getServiceUsername()
  {
    return $this->serviceUsername;
  } 

  /**
   * Set the service's password
   */
  public function setServicePassword($password)
  {
    if ($password !== null)
      $this->servicePassword = $password;
  }

  /**
   * Get the service's password
   */
  public function getServicePassword()
  {
    return $this->servicePassword;
  } 
}
