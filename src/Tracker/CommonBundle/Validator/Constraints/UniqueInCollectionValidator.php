<?php

namespace Tracker\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Form\Util\PropertyPath;

/**
 * Validator for the UniqueInCollection constraint.
 *
 * Help from: http://stackoverflow.com/questions/11782056/how-to-validate-unique-entities-in-an-entity-collection-in-symfony2
 */
class UniqueInCollectionValidator extends ConstraintValidator
{
  // Keep track of which values we've already looked at.
  protected $collectionValues = array();

  public function isValid($value, Constraint $constraint)
  {
    $valid = true;

    // Get the property's value if the property path was specified
    if ($constraint->propertyPath != null)
    {
      $propertyPath = new PropertyPath($constraint->propertyPath);
      $value = $propertyPath->getValue($value);
    }

    // Is this value already used by another object in the collection?
    if (in_array($value, $this->collectionValues))
    {
      $valid = false;
      $this->setMessage($constraint->message, array('%value%' => $value));
    }

    $this->collectionValues[] = $value;

    return $valid;
  }
}
