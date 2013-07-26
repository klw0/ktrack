<?php

namespace Tracker\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Used to transform a string of elements combined with newline delimters into an array and vice versa.
 */
class StringToArrayTransformer implements DataTransformerInterface
{
  private $delimiter;

  public function __construct($delimiter)
  {
    if ($delimiter === null)
      throw new \Exception('Delimiter cannot be null.');

    $this->delimiter = $delimiter;
  }

  /**
   * Transforms the array into a string, with the elements combined using the delimeter.
   */
  public function transform($val)
  {
    if ($val === null)
      return null;

    return implode($this->delimiter, $val);
  }

  /**
   * Transforms the string into an array.
   */
  public function reverseTransform($val)
  {
    if ($val === null)
      return array();

    // Explode and remove whitespace from each item
    $items = explode($this->delimiter, $val);
    return array_map('trim', $items);
  }
}
