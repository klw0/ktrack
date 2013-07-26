<?php

namespace Tracker\StatsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DaypartStatsFilterType extends AbstractType
{
  protected $preferredCampaigns;

  public function __construct($preferredCampaigns = array())
  {
    $this->preferredCampaigns = $preferredCampaigns;
  }

  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('campaigns', 'entity', array(
      'class'             => 'TrackerCampaignBundle:Campaign',
      'preferred_choices' => $this->preferredCampaigns,
    ));

    $builder->add('startDate', 'date', array(
      'widget' => 'single_text',
    ));

    $builder->add('endDate', 'date', array(
      'widget' => 'single_text',
    ));
  }

  public function getName()
  {
    return 'daypart_stats_filter';
  }
}
