<?php

namespace Tracker\TrafficSourceBundle\Wrapper;

/**
 * An interface that all traffic source API/scraper wrappers must implement.
 */
interface TrafficSourceWrapperInterface
{
  /**
   * Returns an array of target data pulled from the traffic source. Each target is a hash of the form:
   *   {name: x, active: y, currentBid: z, creativeIdentifier: q}
   */
  public function getTargetDetailsForCreative($creativeId);

  /**
   * Sets the API key to use when authenticating.
   */
  public function setApiKey($key);

  /**
   * Sets the username and password to use when authenticating.
   */
  public function setUsernameAndPassword($username, $password);
}
