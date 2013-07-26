<?php

namespace Tracker\AffiliateNetworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tracker\AffiliateNetworkBundle\Entity\AffiliateNetwork;
use Tracker\AffiliateNetworkBundle\Form\AffiliateNetworkType;

/**
 * AffiliateNetwork controller.
 *
 * @Route("/affiliatenetwork")
 */
class AffiliateNetworkController extends Controller
{
  /**
   * Lists all AffiliateNetwork entities.
   *
   * @Route("/", name="affiliatenetwork")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getEntityManager();
    $networks = $em->getRepository('TrackerAffiliateNetworkBundle:AffiliateNetwork')->findAll();

    return array('networks' => $networks);
  }

  /**
   * Displays a form to create a new AffiliateNetwork entity.
   *
   * @Route("/new", name="affiliatenetwork_new")
   * @Template()
   */
  public function newAction()
  {
    $network = new AffiliateNetwork();
    $form = $this->createForm(new AffiliateNetworkType(), $network);

    return array(
      'network' => $network,
      'form'    => $form->createView()
    );
  }

  /**
   * Creates a new AffiliateNetwork entity.
   *
   * @Route("/create", name="affiliatenetwork_create")
   * @Method("post")
   * @Template("TrackerAffiliateNetworkBundle:AffiliateNetwork:new.html.twig")
   */
  public function createAction()
  {
    $network = new AffiliateNetwork();
    $request = $this->getRequest();
    $form = $this->createForm(new AffiliateNetworkType(), $network);
    $form->bindRequest($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getEntityManager();
      $em->persist($network);
      $em->flush();

      return $this->redirect($this->generateUrl('affiliatenetwork'));
    }

    return array(
      'network' => $network,
      'form'    => $form->createView()
    );
  }

  /**
   * Displays a form to edit an existing AffiliateNetwork entity.
   *
   * @Route("/{id}/edit", name="affiliatenetwork_edit")
   * @Template()
   */
  public function editAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $network = $em->getRepository('TrackerAffiliateNetworkBundle:AffiliateNetwork')->find($id);

    if (!$network)
      throw $this->createNotFoundException('Unable to find AffiliateNetwork entity.');

    $editForm = $this->createForm(new AffiliateNetworkType(), $network);

    return array(
      'network'      => $network,
      'form'    => $editForm->createView(),
    );
  }

  /**
   * Edits an existing AffiliateNetwork entity.
   *
   * @Route("/{id}/update", name="affiliatenetwork_update")
   * @Method("post")
   * @Template("TrackerAffiliateNetworkBundle:AffiliateNetwork:edit.html.twig")
   */
  public function updateAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $network = $em->getRepository('TrackerAffiliateNetworkBundle:AffiliateNetwork')->find($id);

    if (!$network)
      throw $this->createNotFoundException('Unable to find AffiliateNetwork entity.');

    $editForm   = $this->createForm(new AffiliateNetworkType(), $network);

    $request = $this->getRequest();
    $editForm->bindRequest($request);

    if ($editForm->isValid())
    {
      $em->persist($network);
      $em->flush();

      return $this->redirect($this->generateUrl('affiliatenetwork'));
    }

    return array(
      'network'      => $network,
      'form'   => $editForm->createView(),
    );
  }

  /**
   * Deletes a AffiliateNetwork entity.
   *
   * @Route("/{id}/delete", name="affiliatenetwork_delete")
   */
  public function deleteAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $network = $em->getRepository('TrackerAffiliateNetworkBundle:AffiliateNetwork')->find($id);

    if (!$network)
      throw $this->createNotFoundException('Unable to find AffiliateNetwork entity.');

    $em->remove($network);
    $em->flush();

    return $this->redirect($this->generateUrl('affiliatenetwork'));
  }
}
