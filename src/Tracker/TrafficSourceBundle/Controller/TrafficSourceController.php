<?php

namespace Tracker\TrafficSourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tracker\TrafficSourceBundle\Entity\TrafficSource;
use Tracker\TrafficSourceBundle\Form\TrafficSourceType;

/**
 * TrafficSource controller.
 *
 * @Route("/trafficsource")
 */
class TrafficSourceController extends Controller
{
  /**
   * Lists all TrafficSource entities.
   *
   * @Route("/", name="trafficsource")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getEntityManager();
    $sources = $em->getRepository('TrackerTrafficSourceBundle:TrafficSource')->findAll();

    return array('sources' => $sources);
  }

  /**
   * Displays a form to create a new TrafficSource entity.
   *
   * @Route("/new", name="trafficsource_new")
   * @Template()
   */
  public function newAction()
  {
    $source = new TrafficSource();
    $form = $this->createForm(new TrafficSourceType(), $source);

    return array(
      'source' => $source,
      'form'   => $form->createView()
    );
  }

  /**
   * Creates a new TrafficSource entity.
   *
   * @Route("/create", name="trafficsource_create")
   * @Method("post")
   * @Template("TrackerTrafficSourceBundle:TrafficSource:new.html.twig")
   */
  public function createAction()
  {
    $source = new TrafficSource();
    $request = $this->getRequest();
    $form = $this->createForm(new TrafficSourceType(), $source);
    $form->bindRequest($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getEntityManager();
      $em->persist($source);
      $em->flush();

      $this->get('session')->setFlash('success', '<strong>Ya mon!</strong> The traffic source was created successfully.');
      return $this->redirect($this->generateUrl('trafficsource_edit', array('id' => $source->getId())));
    }

    return array(
      'source' => $source,
      'form'   => $form->createView()
    );
  }

  /**
   * Displays a form to edit an existing TrafficSource entity.
   *
   * @Route("/{id}/edit", name="trafficsource_edit")
   * @Template()
   */
  public function editAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $source = $em->getRepository('TrackerTrafficSourceBundle:TrafficSource')->find($id);

    if (!$source)
      throw $this->createNotFoundException('Unable to find TrafficSource entity.');

    $editForm = $this->createForm(new TrafficSourceType(), $source);

    return array(
      'source'  => $source,
      'form'    => $editForm->createView(),
    );
  }

  /**
   * Edits an existing TrafficSource entity.
   *
   * @Route("/{id}/update", name="trafficsource_update")
   * @Method("post")
   * @Template("TrackerTrafficSourceBundle:TrafficSource:edit.html.twig")
   */
  public function updateAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $source = $em->getRepository('TrackerTrafficSourceBundle:TrafficSource')->find($id);

    if (!$source)
      throw $this->createNotFoundException('Unable to find TrafficSource entity.');

    $editForm = $this->createForm(new TrafficSourceType(), $source);

    $request = $this->getRequest();
    $editForm->bindRequest($request);

    if ($editForm->isValid())
    {
      $em->persist($source);
      $em->flush();

      $this->get('session')->setFlash('success', '<strong>Ya me bredren!</strong> The traffic source was updated successfully.');
      return $this->redirect($this->generateUrl('trafficsource_edit', array('id' => $source->getId())));
    }

    return array(
      'source' => $source,
      'form'   => $editForm->createView(),
    );
  }

  /**
   * Deletes a TrafficSource entity.
   *
   * @Route("/{id}/delete", name="trafficsource_delete")
   */
  public function deleteAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $source = $em->getRepository('TrackerTrafficSourceBundle:TrafficSource')->find($id);

    if (!$source)
      throw $this->createNotFoundException('Unable to find TrafficSource entity.');

    $em->remove($source);
    $em->flush();

    return $this->redirect($this->generateUrl('trafficsource'));
  }
}
