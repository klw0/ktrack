<?php

namespace Tracker\TrafficSourceBundle\Wrapper;

/**
 * This class wraps the Trafficvance API (API v2.0).
 */
class Trafficvance implements TrafficSourceWrapperInterface
{
  private $apiKey;
  private $targetsClient;

  /**
   * Returns an array of target data pulled from Trafficvance. Each target is a hash of the form:
   *   {name: x, active: y, currentBid: z, creativeIdentifier: q}
   *
   * Note: The SOAP calls may throw exceptions, always wrap this in a try/catch.
   */
  public function getTargetDetailsForCreative($creativeId)
  {
    $client = $this->getTargetsClient();

    // Get the target ids
    $targetIds = $client->getTargetIDs(array('creativeID' => $creativeId))->CTIDs;

    // Make sure the creative was found, in which case the item property would be set
    if (!isset($targetIds->item))
      throw new \Exception("Creative with identifier '$creativeId' was not found.");

    // The item property contains an array of target ids
    $targetIds = $targetIds->item;
    $targetBasicDetails = array();
    $targetAdvancedDetails = array();

    // Trafficvance's API limits us to only getting details for 1000 targets at a time.
    $maxTargetDetailsArrayLength = 1000;
    for ($i = 0; $i < count($targetIds); $i += $maxTargetDetailsArrayLength)
    {
      $targetIdsSlice = array_slice($targetIds, $i, $maxTargetDetailsArrayLength);

      // Merge the new target detail items into the existing target detail item arrays.
      $targetBasicDetails = array_merge($targetBasicDetails, $client->getTargetBasicDetails(array('targetIDs' => $targetIdsSlice))->details->item);
      $targetAdvancedDetails = array_merge($targetAdvancedDetails, $client->getTargetAdvancedDetails(array('targetIDs' => $targetIdsSlice))->details->item);
    }

    // Merge and transform the target detail arrays
    $targetDetails = array();
    foreach ($targetBasicDetails as $targetBasicDetail)
    {
      foreach ($targetAdvancedDetails as $targetAdvancedDetail)
      {
        // Are the target ids the same?
        if ($targetBasicDetail->CTID->targetID == $targetAdvancedDetail->CTID->targetID)
        {
          $targetDetails[] = array(
            'name'                => $targetBasicDetail->target,
            'active'              => ($targetBasicDetail->status == 'ACTIVE'),
            'currentBid'          => $targetAdvancedDetail->myBid,
            'creativeIdentifier'  => $creativeId,
          );
        }
      }
    }

    return $targetDetails;
  }

  /**
   * Sets the API key to use when authenticating.
   */
  public function setApiKey($key)
  {
    $this->apiKey = $key;
  }

  /**
   * Sets the username and password to use when authenticating.  Unused for this wrapper.
   */
  public function setUsernameAndPassword($username, $password) {
  }

  /**
   * Returns the SOAP client needed to interact with TV's targets service.
   */
  private function getTargetsClient()
  {
    if (!$this->targetsClient)
      $this->targetsClient = $this->createClient('targets');

    return $this->targetsClient;
  }

  /**
   * Creates and sets up a SOAP client for the given TV service.
   */
  private function createClient($serviceName)
  {
    $client = new \SoapClient(
      'https://api.trafficvance.com/?v2=' . $serviceName . '.wsdl',
      array('soap_version'  => '1.2',
      'connect_timeout'     => 10,
      'compression'         => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
      'cache_wdsl'          => WSDL_CACHE_NONE
    ));

    $header = new \SoapHeader(
      'https://api.trafficvance.com/?v2=system.wsdl',
      'AuthenticateRequest',
      new \SoapVar(array('apiKey' => $this->apiKey), SOAP_ENC_OBJECT),
      true
    );

    $client->__setSoapHeaders($header);

    return $client;
  }
}
