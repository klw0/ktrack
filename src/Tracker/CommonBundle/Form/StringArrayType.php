<?php

namespace Tracker\CommonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Tracker\CommonBundle\Form\DataTransformer\StringToArrayTransformer;

/**
 * A type that is the string representation of an array.
 */
class StringArrayType extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $transformer = new StringToArrayTransformer($options['delimiter']);
    $builder->appendClientTransformer($transformer);
  }

  public function getDefaultOptions(array $options)
  {
    // Default delimiter is a comma
    $defaultOptions = array(
      'delimiter' => isset($options['delimiter']) ? $options['delimiter'] : ',',
    );

    return $defaultOptions;
  }

  public function getParent(array $options)
  {
    return 'textarea';
  }

  public function getName()
  {
    return 'string_array';
  }
}
