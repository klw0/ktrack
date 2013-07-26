<?php

namespace Tracker\CampaignBundle\Form;

use Tracker\CampaignBundle\Entity\Campaign;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CampaignType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder
      ->add('name', 'text')
      ->add('notes', 'textarea')
      ->add('type', 'choice', array('choices' => array_flip(Campaign::getTypes())))
      ->add('estimatedCostPerView', 'money', array(
        'currency' => 'USD',
        'precision' => 4,
      ))
      ->add('active', 'checkbox')
      ->add('trafficSource')
      ->add('creativeIdentifiers', 'string_array', array('delimiter' => "\n"))
      ->add('landingPages', 'collection', array(
        'type' => new LandingPageType(),
        'allow_add' => true,
        'prototype' => true,
        'by_reference' => false,    // required so Campaign::setLandingPages() is called
      ))
      ->add('offerGroups', 'collection', array(
        'type' => new OfferGroupType(),
        'allow_add' => true,
        'prototype' => true,
        'by_reference' => false,    // required so Campaign::setOfferGroups() is called
      ))
      ;
  }

  public function getDefaultOptions(array $options)
  {
    return array(
      'data_class' => 'Tracker\CampaignBundle\Entity\Campaign',
    );
  }

  public function getName()
  {
    return 'campaign';
  }
}
