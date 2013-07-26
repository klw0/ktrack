<?php

namespace Tracker\CampaignBundle\Controller;

use Tracker\CampaignBundle\Entity\Campaign;
use Tracker\CampaignBundle\Form\CampaignType;
use Tracker\CampaignBundle\Entity\LandingPage;
use Tracker\CampaignBundle\Entity\OfferGroup;
use Tracker\CampaignBundle\Entity\Offer;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Campaign controller.
 *
 * @Route("/campaign")
 */
class CampaignController extends Controller
{
  /**
   * Lists all Campaign entities.
   *
   * @Route("/", name="campaign")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getEntityManager();

    $activeCampaigns = $em->getRepository('TrackerCampaignBundle:Campaign')->findBy(array('active' => true));
    $inactiveCampaigns = $em->getRepository('TrackerCampaignBundle:Campaign')->findBy(array('active' => false));

    return array(
      'active_campaigns' => $activeCampaigns,
      'inactive_campaigns' => $inactiveCampaigns,
    );
  }

  /**
   * Displays a form to create a new Campaign entity.
   *
   * @Route("/new", name="campaign_new")
   * @Template()
   */
  public function newAction()
  {
    $campaign = new Campaign();

    // Each campaign requires at least one group with one offer
    $offerGroup = new OfferGroup();
    $offerGroup->addOffer(new Offer());
    $campaign->addOfferGroup($offerGroup);

    $form = $this->createForm(new CampaignType(), $campaign);

    return array(
      'campaign' => $campaign,
      'form' => $form->createView()
    );
  }

  /**
   * Creates a new Campaign entity.
   *
   * @Route("/create", name="campaign_create")
   * @Method("post")
   * @Template("TrackerCampaignBundle:Campaign:new.html.twig")
   */
  public function createAction()
  {
    $request = $this->getRequest();

    $campaign = new Campaign();

    // Each campaign requires at least one group with one offer
    $offerGroup = new OfferGroup();
    $offerGroup->addOffer(new Offer());
    $campaign->addOfferGroup($offerGroup);

    $form = $this->createForm(new CampaignType(), $campaign);
    $form->bindRequest($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getEntityManager();
      $em->persist($campaign);
      $em->flush();

      $this->get('session')->setFlash('success', '<strong>Success!</strong> The campaign was created successfully.');
      return $this->redirect($this->generateUrl('campaign_edit', array('id' => $campaign->getId())));
    }

    $this->get('session')->setFlash('error', '<strong>Error!</strong> The campaign could not be created.');
    return array(
      'campaign' => $campaign,
      'form'   => $form->createView()
    );
  }

  /**
   * Displays a form to edit an existing Campaign entity.
   *
   * @Route("/{id}/edit", name="campaign_edit")
   * @Template()
   */
  public function editAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($id);

    if (!$campaign)
      throw $this->createNotFoundException('Unable to find Campaign entity.');

    $form = $this->createForm(new CampaignType(), $campaign);

    return array(
      'campaign' => $campaign,
      'form'   => $form->createView(),
    );
  }

  /**
   * Edits an existing Campaign entity.
   *
   * @Route("/{id}/update", name="campaign_update")
   * @Method("post")
   * @Template("TrackerCampaignBundle:Campaign:edit.html.twig")
   */
  public function updateAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($id);

    if (!$campaign)
      throw $this->createNotFoundException('Unable to find Campaign entity.');

    $form = $this->createForm(new CampaignType(), $campaign);

    $request = $this->getRequest();
    $form->bindRequest($request);

    if ($form->isValid()) 
    {
      $em->persist($campaign);
      $em->flush();

      $this->get('session')->setFlash('success', '<strong>Success!</strong> The campaign was updated successfully.');
      return $this->redirect($this->generateUrl('campaign_edit', array('id' => $id)));
    }

    $this->get('session')->setFlash('error', '<strong>Error!</strong> The campaign could not be updated.');
    return array(
      'campaign' => $campaign,
      'form'   => $form->createView(),
    );
  }

  /**
   * Deletes a Campaign entity.
   *
   * @Route("/{id}/delete", name="campaign_delete")
   */
  public function deleteAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($id);

    if (!$campaign)
      throw $this->createNotFoundException('Unable to find Campaign entity.');

    $em->remove($campaign);
    $em->flush();

    return $this->redirect($this->generateUrl('campaign'));
  }

  /**
   * Resets a campaign's stats.
   *
   * @Route("/{id}/reset", name="campaign_reset_stats")
   */
  public function resetStatsAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($id);

    if (!$campaign)
      throw $this->createNotFoundException('Unable to find Campaign entity.');

    // Reset the stats
    $campaign->resetStats();
    $em->flush();

    $this->get('session')->setFlash('success', "<strong>Alright!</strong> The stats were reset for \"$campaign\".");
    return $this->redirect($this->generateUrl('campaign'));
  }

  /**
   * Changes a campaign's active status.
   *
   * @Route("/{id}/active", name="campaign_active")
   */
  public function toggleActiveAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($id);

    if (!$campaign)
      throw $this->createNotFoundException('Unable to find Campaign entity.');

    // Toggle the active status
    $campaign->setActive(!$campaign->getActive());
    $em->persist($campaign);
    $em->flush();

    $this->get('session')->setFlash('success', "<strong>Alright!</strong> \"$campaign\" was " . ($campaign->isActive() ? 'activated' : 'deactivated') . ".");
    return $this->redirect($this->generateUrl('campaign'));
  }

  /**
   * Clones a campaign.
   *
   * @Route("/{id}/clone", name="campaign_clone")
   */
  public function cloneAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($id);

    if (!$campaign)
      throw $this->createNotFoundException('Unable to find Campaign entity.');

    // Clone the campaign
    $clonedCampaign = clone $campaign;
    $em->persist($clonedCampaign);
    $em->flush();

    $this->get('session')->setFlash('success', "<strong>Yipideedoo!</strong> \"$campaign\" was cloned into \"$clonedCampaign\".");
    return $this->redirect($this->generateUrl('campaign'));
  }
}
