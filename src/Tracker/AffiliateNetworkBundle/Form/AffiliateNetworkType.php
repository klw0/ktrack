<?php

namespace Tracker\AffiliateNetworkBundle\Form;

use Tracker\AccountManagerBundle\Form\AccountManagerType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AffiliateNetworkType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder
      ->add('name')
      ->add('shortName', 'text', array('label' => 'Short Name'))
      ->add('siteUrl', 'url', array('label' => 'Website'))
      ->add('accountManager', new AccountManagerType(), array('label' => 'Account Manager'))
      ;
  }

  public function getDefaultOptions(array $options)
  {
    return array(
      'data_class' => 'Tracker\AffiliateNetworkBundle\Entity\AffiliateNetwork',
    );
  }

  public function getName()
  {
    return 'affiliatenetwork';
  }
}
