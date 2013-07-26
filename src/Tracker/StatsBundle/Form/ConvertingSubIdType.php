<?php

namespace Tracker\StatsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ConvertingSubIdType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('subIds', 'string_array', array('delimiter' => "\n"));
    $builder->add('overrideExistingConversions', 'checkbox', array(
      'label' => 'Override existing conversions for these sub ids?',
      'data' => true,
    ));
  }

  public function getName()
  {
    return 'converting_subid';
  }
}
