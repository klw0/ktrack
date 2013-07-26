<?php

namespace Tracker\CampaignBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class LandingPageType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder
      ->add('name')
      ->add('description')
      ->add('url')
      ->add('viewKeyParameter', null, array('data' => 'v'))
      ->add('wantsTarget')
      ->add('targetParameter', null, array('data' => 't'))
      ->add('weight', 'percent', array('type' => 'integer'))
      ;
  }

  public function getDefaultOptions(array $options)
  {
    return array(
      'data_class' => 'Tracker\CampaignBundle\Entity\LandingPage',
    );
  }

  public function getName()
  {
    return 'landingpage';
  }
}
