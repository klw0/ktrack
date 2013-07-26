<?php

namespace Tracker\CommonBundle\Twig\Extension;

use \NumberFormatter;

/**
 * Some handy dandy Twig extensions for this project
 */
class TrackerExtension extends \Twig_Extension
{
  /**
   * Returns a list of filters.
   *
   * @return array
   */
  public function getFilters()
  {
    return array(
      'money' => new \Twig_Filter_Method($this, 'money_filter'),
      'percent' => new \Twig_Filter_Method($this, 'percent_filter'),

      'urldecode' => new \Twig_Filter_Method($this, 'urldecode_filter'),
    );
  }

  /**
   * Name of this extension
   *
   * @return string
   */
  public function getName()
  {
    return 'Tracker';
  }

  /**
   * Formats the input as money
   *
   * @return string
   */
  public function money_filter($number, $decimals = 2)
  {
    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
    $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
    return $formatter->formatCurrency($number, 'USD');
  }

  /**
   * Formats the input as a percent
   *
   * @return string
   */
  public function percent_filter($number)
  {
    $formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT);
    $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
    $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);
    return $formatter->format($number);
  }

  /**
   * Decodes the URL
   *
   * @return string
   */
  public function urldecode_filter($url, $raw = false)
  {
    if ($raw)
      return rawurldecode($url);

    return urldecode($url);
  }
}

