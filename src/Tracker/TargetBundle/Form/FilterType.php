<?php

namespace Tracker\TargetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FilterType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('campaigns', 'entity', array(
      'class' => 'TrackerCampaignBundle:Campaign',
    ));
  }

  public function getName()
  {
    return 'filter';
  }
}
