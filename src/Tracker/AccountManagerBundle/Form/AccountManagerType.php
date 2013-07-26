<?php

namespace Tracker\AccountManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AccountManagerType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder
      ->add('name')
      ->add('email', 'email')
      ->add('phone')
      ->add('notes', 'textarea', array('attr' => array('rows' => 5)))
      ;
  }

  public function getDefaultOptions(array $options)
  {
    return array(
      'data_class' => 'Tracker\AccountManagerBundle\Entity\AccountManager',
    );
  }

  public function getName()
  {
    return 'accountmanager';
  }
}
