<?php

namespace Tracker\CommonBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Tracker\CampaignBundle\Entity\Campaign;
use Tracker\CampaignBundle\Entity\LandingPage;
use Tracker\CampaignBundle\Entity\Offer;
use Tracker\CampaignBundle\Entity\OfferGroup;
use Tracker\TrackingBundle\Entity\View;
use Tracker\TrackingBundle\Entity\Click;
use Tracker\TrackingBundle\Entity\Conversion;

class LoadAllData implements FixtureInterface
{
  public function load($manager)
  {
    ini_set('memory_limit', '2048M');

    // Create a campaign
    $campaign = new Campaign();
    $campaign->setName('Fixture Campaign');
    $campaign->setType('direct_link');
    $campaign->setEstimatedCostPerView(0.015);
    $campaign->setActive(true);

    $manager->persist($campaign);

    // Create an offer group for the campaign
    $offerGroup = new OfferGroup();
    $offerGroup->setName('Fixture Offer Group');
    $offerGroup->setCampaign($campaign);

    $manager->persist($offerGroup);

    // Create an offer for the campaign
    $offer = new Offer();
    $offer->setName('Fixture Offer 1');
    $offer->setUrl('http://offer.com');
    $offer->setPayout('1.75');
    $offer->setWeight('100');
    $offer->setNetworkOfferId('0');
    $offer->setOfferGroup($offerGroup);

    $manager->persist($offer);
    $manager->flush();

    $campaignId = $campaign->getId();

    // Create some views for the campaign
    $numViews = 30000;
    $numTargets = 1000;
    $targets = array();
    for ($i = 0; $i < $numTargets; $i++)
    {
      $targets[] = 'target' . $i;
    }

    $conn = $manager->getConnection();
    for ($i = 0; $i < $numViews; $i++)
    {
      $conn->insert('View', array(
        'targetName' => $targets[rand(0, $numTargets - 1)],
        'cost' => '0.015',
        'campaign_id' => $campaignId,
        //'createdAt' => $date,
      ));
    }
  }
}
