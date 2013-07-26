<?php

namespace Tracker\CampaignBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class OfferGroupType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder
      ->add('name')
      ->add('offers', 'collection', array(
        'type' => new OfferType(),
        'allow_add' => true,
        'prototype' => true,
        'by_reference' => false,    // required so OfferGroup::setOffers() is called
      ))
      ;
  }

  public function getDefaultOptions(array $options)
  {
    return array(
      'data_class' => 'Tracker\CampaignBundle\Entity\OfferGroup',
    );
  }

  public function getName()
  {
    return 'offergroup';
  }
}
