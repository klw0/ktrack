<?php

namespace Tracker\StatsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CampaignStatsFilterType extends AbstractType
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

    $builder->add('startTime', 'time', array(
      'input'        => 'array',
      'widget'       => 'single_text',
      'with_seconds' => true,
    ));

    $builder->add('endDate', 'date', array(
      'widget' => 'single_text',
    ));

    $builder->add('endTime', 'time', array(
      'input'        => 'array',
      'widget'       => 'single_text',
      'with_seconds' => true,
    ));

    $builder->add('targetGroupByLandingPage', 'checkbox');
    $builder->add('targetGroupByOffer', 'checkbox');

    $builder->add('targetGroupByExtraData1', 'checkbox');
    $builder->add('targetGroupByExtraData2', 'checkbox');
    $builder->add('targetGroupByExtraData3', 'checkbox');
    $builder->add('targetGroupByExtraData4', 'checkbox');
    $builder->add('targetGroupByExtraData5', 'checkbox');

    $builder->add('landingPageGroupByOffer', 'checkbox');
  }

  public function getName()
  {
    return 'campaign_stats_filter';
  }
}
