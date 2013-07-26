<?php

namespace Tracker\TrackingBundle\Controller;

use Tracker\CampaignBundle\Entity\Campaign;
use Tracker\CampaignBundle\Entity\LandingPage;
use Tracker\TrackingBundle\Entity\View;
use Tracker\TrackingBundle\Entity\Click;
use Tracker\TrackingBundle\Entity\Conversion;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Tracking controller.
 */
class TrackingController extends Controller
{
  /**
   * Tracks a view and redirects to the offer or landing page
   *
   * @Route("/track/view/{campaignKey}/", name="track_view")
   */
  public function viewAction($campaignKey, Request $request)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find(Campaign::decodeKey($campaignKey));

    // Make sure we have an existing campaign
    if (!$campaign)
      throw $this->createNotFoundException('Unable to find Campaign entity.');

    // Get the target parameter value, set the default target name to 'N/A'
    //$target = $request->get('target', 'N/A');
    $target = $request->get($campaign->getTrafficSource()->getTargetParameter(), 'N/A');

    // Get the cost of the view (the target's current bid price or the campaign's estimated CPV)
    $cost = $em->getRepository('TrackerTargetBundle:Target')->getTargetCost($target, $campaign);

    // Get the destination (either an offer or a landing page)
    $destination = $campaign->getDestination();
    $destinationUrl = null;

    // Track the view
    $view = new View();
    $view->setTargetName($target);
    $view->setCost($cost);
    $view->setIp($request->getClientIp());
    $view->setCampaign($campaign);

    // Capture any extra data passed along in the query string parameters d1, d2, ...
    $rawParams = $this->getRawQueryStringParams();
    for ($i = 1; $i <= 5; $i++)
    {
      if (array_key_exists("d$i", $rawParams))
        $view->{"setExtraData$i"}($rawParams["d$i"]);
    }

    if ($destination instanceOf LandingPage)
    {
      $view->setLandingPage($destination);

      $em->persist($view);
      $em->flush();

      // Get the redirect url
      $destinationUrl = $destination->getRedirectUrl($view->getKey(), $target);
    }
    else
    {
      // The destination must be an offer, so track a click to the offer as well
      $click = new Click();
      $click->setOffer($destination);
      $click->setView($view);

      $em->persist($click);
      $em->persist($view);
      $em->flush();

      $destinationUrl = $destination->getRedirectUrl($click->getKey());
    }

    //return new Response("view key: " . $view->getKey() . ", campaign id: " . $campaign->getId() . ", target: $target, destination url: " . $destinationUrl . ", target cost: $cost");
    return $this->redirect($destinationUrl);
  }

  /**
   * Tracks a click and redirects to the offer
   *
   * @Route("/track/click/{viewKey}/", defaults={"offerGroupName" = null}, name="track_click")
   * @Route("/track/click/{viewKey}/{offerGroupName}/", name="track_click_with_group")
   */
  public function clickAction($viewKey, $offerGroupName)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $view = $em->getRepository('TrackerTrackingBundle:View')->find(View::decodeKey($viewKey));

    // Make sure the view exists
    if (!$view)
      throw $this->createNotFoundException('Unable to find View entity.');

    // Get the offer group
    $offerGroup = $em->getRepository('TrackerCampaignBundle:OfferGroup')->findOneBy(array(
      'campaign' => $view->getCampaign()->getId(),
      'name'     => $offerGroupName,
    ));

    // If the offer group wasn't found or no group was specified, use the first one
    if (!$offerGroup)
    {
      $offerGroups = $view->getCampaign()->getOfferGroups();
      $offerGroup = $offerGroups[0];
    }

    // Check if a click already exists for this view and offer group
    $existingClick = $em->getRepository('TrackerTrackingBundle:Click')->findOneByViewAndOfferGroup($view, $offerGroup);
    if ($existingClick)
    {
      // Use the existing click
      $click = $existingClick;
    }
    else
    {
      // Create a new click
      $click = new Click();
      $click->setOffer($offerGroup->getRandomOffer());
      $click->setView($view);
      $em->persist($click);
      $em->flush();
    }

    $offerUrl = $click->getOffer()->getRedirectUrl($click->getKey(), $this->getRequest()->getQueryString());

    //return new Response("sub id: " . $click->getKey() . ", offer url: " . $offerUrl . ", offer group: " . $offerGroup->getName());
    return $this->redirect($offerUrl);
  }

  /**
   * Tracks a conversion.
   * Note: the sub id is the key of the related click.
   *
   * @Route("/track/conversion/{subId}/", defaults={"revenue" = null}, name="track_conversion")
   * @Route("/track/conversion/{subId}/{revenue}", name="track_conversion_with_revenue")
   */
  public function conversionAction($subId, $revenue)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $click = $em->getRepository('TrackerTrackingBundle:Click')->find(Click::decodeKey($subId));

    // Make sure the click exists
    if (!$click)
      throw $this->createNotFoundException('Unable to find Click entity.');

    // If there was no revenue passed in, use the offer's payout as a fallback revenue amount
    if (!$revenue)
      $revenue = $click->getOffer()->getPayout();

    // Track the conversion
    $conversion = new Conversion();
    $conversion->setRevenue($revenue);
    $conversion->setClick($click);

    $em->persist($conversion);
    $em->flush();

    //return new Response("revenue: " . $revenue);
    return new Response();
  }

  /**
   * Returns an array of the query string parameters in their raw form (not URL decoded like $_GET provides).
   */
  private function getRawQueryStringParams()
  {
    $params = array();
    $qs = $_SERVER['QUERY_STRING'];

    if (empty($qs)) return $params;

    $qsPairs = explode('&', $qs);
    foreach ($qsPairs as $qsPair)
    {
      $split = explode('=', $qsPair);

      if ($split !== false && count($split) == 2)
        $params[$split[0]] = $split[1];
    }

    return $params;
  }
}
