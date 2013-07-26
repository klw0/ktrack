<?php

namespace Tracker\CampaignBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class OfferType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder
      ->add('name')
      ->add('description')
      ->add('url')
      ->add('wantsSubId')
      ->add('wantsQueryString')
      ->add('payout', 'money', array(
        'currency' => 'USD',
        'precision' => 2,
      ))
      ->add('affiliateNetwork')
      ->add('weight', 'percent', array('type' => 'integer'))
      ;
  }

  public function getDefaultOptions(array $options)
  {
    return array(
      'data_class' => 'Tracker\CampaignBundle\Entity\Offer',
    );
  }

  public function getName()
  {
    return 'offer';
  }
}
