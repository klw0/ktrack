<?php

namespace Tracker\TrafficSourceBundle\Form;

use Tracker\TrafficSourceBundle\Entity\TrafficSource;
use Tracker\AccountManagerBundle\Form\AccountManagerType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TrafficSourceType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder
      ->add('name')
      ->add('targetParameter', 'text', array('label' => 'Target Parameter'))
      ->add('targetToken', 'text', array('label' => 'Target Token'))
      ->add('serviceName', 'text', array('label' => 'Service Name'))
      ->add('serviceAuthenticationMethod', 'choice', array(
        'choices' => array_flip(TrafficSource::getServiceAuthenticationMethods()),
        'label' => 'Service Authentication Method'
      ))
      ->add('serviceApiKey', 'text', array('label' => 'Service API Key'))
      ->add('serviceUsername', 'text', array('label' => 'Service Username'))
      ->add('servicePassword', 'password', array(
        'always_empty' => false,
        'label' => 'Service Password',
      ))
      ->add('siteUrl', 'url', array('label' => 'Website'))
      ->add('accountManager', new AccountManagerType(), array('label' => 'Account Manager'))
      ;
  }

  public function getDefaultOptions(array $options)
  {
    return array(
      'data_class' => 'Tracker\TrafficSourceBundle\Entity\TrafficSource',
    );
  }

  public function getName()
  {
    return 'trafficsource';
  }
}
