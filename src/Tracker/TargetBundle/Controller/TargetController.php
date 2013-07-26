<?php

namespace Tracker\TargetBundle\Controller;

use Tracker\TargetBundle\Form\FilterType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * TrafficSource controller.
 *
 * @Route("/target")
 */
class TargetController extends Controller
{
  /**
   * Lists all targets for a campaign.
   *
   * @Route("/", defaults={"campaignId" = null}, name="targets")
   * @Route("/campaign/{campaignId}", requirements={"campaignId" = "\d+"}, name="targets_campaign")
   * @Template()
   * @Method({"GET", "POST"})
   */
  public function indexAction($campaignId, Request $request)
  {
    $campaign = null;

    // Did the filter form POST here?
    if ($request->getMethod() == 'POST')
    {
      $form = $this->createForm(new FilterType());
      $form->bindRequest($request);

      if ($form->isValid())
      {
        $data = $form->getData();
        $campaign = $data['campaigns'];
      }
    }
    else
    {
      $em = $this->getDoctrine()->getEntityManager();

      $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($campaignId);
      if (!$campaign)
      {
        // No campaign was specified ($campaignId was null), so get the first campaign created
        $campaigns = $em->getRepository('TrackerCampaignBundle:Campaign')->findAll();
        $campaign = $campaigns[0];
      }
    }

    // Create the form
    $form = $this->createForm(new FilterType(), array(
      'campaigns' => $campaign,
    ));

    return array(
      'form' => $form->createView(),
      'campaign' => $campaign,
    );
  }

  /**
   * Delete all of the targets for a campaign.
   *
   * @Route("/delete/campaign/{campaignId}", name="targets_delete_campaign")
   */
  public function deleteTargetsAction($campaignId)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($campaignId);

    // Make sure the campaign exists
    if (!$campaign)
      throw $this->createNotFoundException('Unable to find Campaign entity.');

    // Remove the campaign's targets
    $campaign->getTargets()->clear();
    $em->persist($campaign);
    $em->flush();

    return $this->redirect($this->generateUrl('targets_campaign', array('campaignId' => $campaignId)));
  }

  /**
   * Updates the target cache for a campaign.
   *
   * @Route("/update/campaign/{campaignId}", name="targets_update_campaign")
   */
  public function updateTargetsAction($campaignId)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($campaignId);

    // Make sure the campaign exists
    if (!$campaign)
      throw $this->createNotFoundException('Unable to find Campaign entity.');

    // Update the local target cache.  The call may cause exceptions to be thrown, so wrap it in a try/catch.
    try {
      // Make sure the campaign's traffic source has a service
      $trafficSource = $campaign->getTrafficSource();
      if ($trafficSource->hasService())
      {
        $trafficSourceService = $this->get($trafficSource->getServiceName());

        // Set the API key or username/password for the service to use
        if ($trafficSource->serviceAuthenticatesWithApiKey())
          $trafficSourceService->setApiKey($trafficSource->getServiceApiKey());
        else
          $trafficSourceService->setUsernameAndPassword($trafficSource->getServiceUsername(), $trafficSource->getServicePassword());

        // Update the targets
        $trafficSourceService->updateTargetCache($campaign);

        $this->get('session')->setFlash('success', '<strong>Awesome!</strong> The targets were updated successfully.');
      }
      else
        throw new \Exception("The campaign's traffic source does not have a custom service to get target data.");
    } catch (\Exception $e) {
      $this->get('session')->setFlash('error', '<strong>Error!</strong> ' . $e->getMessage());
    }

    return $this->redirect($this->generateUrl('targets_campaign', array('campaignId' => $campaignId)));
  }
}
