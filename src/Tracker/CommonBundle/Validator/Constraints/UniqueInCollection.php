<?php

namespace Tracker\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
* @Annotation
*
* Help from:  http://stackoverflow.com/questions/11782056/how-to-validate-unique-entities-in-an-entity-collection-in-symfony2
*/
class UniqueInCollection extends Constraint
{
  public $message = 'The value "%value%" is not unique.';

  // The property path used to check wether objects are equal.
  // If none is specified, it will check that objects are equal.
  public $propertyPath = null;
}
