<?php

namespace Tracker\TrafficSourceBundle\Wrapper;

/**
 * This class wraps the the details of the MediaTraffic scraping methods.
 */
class MediaTraffic implements TrafficSourceWrapperInterface
{
  const BASE_URL = 'https://www.mediatraffic.com/';
  const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11';

  private $request = null;
  private $username;
  private $password;

  public function __construct()
  {
    $this->request = new \HttpRequest();

    $this->request->enableCookies();
    $this->request->setOptions(array(
      'redirect' => 10,
      'useragent' => self::USER_AGENT,
    ));
  }

  /**
   * Returns an array of target data pulled from MediaTraffic. Each target is a hash of the form:
   *   {name: x, active: y, currentBid: z, creativeIdentifier: q}
   */
  public function getTargetDetailsForCreative($creativeId)
  {
    // Log into MT so that that |$request| gets filled with the proper cookies.
    $this->login($this->username, $this->password);

    $creativeUrl = self::BASE_URL . 'manage/campaign_setup.php?t_bypage=250&' . $creativeId;

    // Get the default page for the creative
    $this->request->setUrl($creativeUrl);
    $this->request->setMethod(HTTP_METH_GET);
    $this->request->send();
    $response = $this->request->getResponseBody();

    // How many pages are there?
    $doc = new \DOMDocument();
    @$doc->loadHtml($response);
    $xpath = new \DOMXPath($doc);

    $navNode = $xpath->query('(//span[@class = "current-nav-text"])[last()]/a[last()]/@title')->item(0);
    if ($navNode !== null)
    {
      preg_match('/(?<=\().*(?=\))/', $navNode->value, $matches);
      $numPages = $matches[0];
    }
    else
    {
      $numPages = 1;
    }

    $targets = array();
    for ($i = 1; $i <= $numPages; $i++)
    {
      // Only get the response if page > 1, because we already have page 1 in |$response|
      if ($i > 1)
      {
        $pageUrl = self::BASE_URL . 'manage/campaign_setup.php?t_bypage=250&t_page=' . $i . '&' . $creativeId;

        $this->request->setUrl($pageUrl);
        $this->request->setMethod(HTTP_METH_GET);
        $this->request->send();
        $response = $this->request->getResponseBody();
      }

      // Pull the keyword data from the page
      $moreTargets = $this->getTargetData($response, $creativeId);

      // Merge these targets with the existing ones
      $targets = array_merge($targets, $moreTargets);
    }

    return $targets;
  }

  /**
   * Sets the API key to use when authenticating.  Unused for this 
   * wrapper.
   */
  public function setApiKey($key) {
  }

  /**
   * Sets the username and password to use when authenticating.
   */
  public function setUsernameAndPassword($username, $password)
  {
    $this->username = $username;
    $this->password = $password;
  }

  /**
   * Log into LI.
   */
  private function login($username, $password)
  {
    $this->request->setUrl(self::BASE_URL);
    $this->request->send();

    // Grab form data
    $formData = $this->getFormData('frmLogin', $this->request->getResponseBody());

    $formFieldOverrides = array(
      'username' => $username,
      'password' => $password,
    );

    $formFields = array_merge($formData['fields'], $formFieldOverrides);
    $response = $this->submitForm($formData['action'], $formData['method'], $formFields);

    if (strpos($response, 'Invalid login!') !== FALSE)
      throw new \Exception('Could not log in to MediaTraffic, invalid credentials.');
  }

  /**
   * Gets target data from the given HTML.
   */
  private function getTargetData($html, $creativeId)
  {
    $doc = new \DOMDocument();
    @$doc->loadHtml($html);

    $xpath = new \DOMXPath($doc);
    // For some reason, PHP's xpath does not like tbody, so /table/tbody/tr becomes /table/tr - wtf?
    $rows = $xpath->query('(//form[@name = "keywords_list"]/table/tr[@valign = "middle"])[position() >= 3]');

    $targets = array();
    foreach ($rows as $row)
    {
      // Name of the target is in the second column
      $name = trim($xpath->query('td[2]/span/strong', $row)->item(0)->textContent);

      // Status of the target is in the sixth column
      $active = ($xpath->query('td[3]/span/strong', $row)->item(0)->textContent == 'active');

      // The current bid/max bid is in the fourth column
      $currentBid = $xpath->query('td[6]/span/strong/input/@value', $row)->item(0)->value;

      $targets[] = array(
        'name'                => $name,
        'active'              => $active,
        'currentBid'          => $currentBid,
        'creativeIdentifier'  => $creativeId,
      );
    }

    return $targets;
  }




  /**
   * Get the value of an attribute.
   */
  private function getAttributeValue($attributeName, \DOMNamedNodeMap $attributes)
  {
    return (($attribute = $attributes->getNamedItem($attributeName)) !== null) ? $attribute->value : null;
  }

  /**
   * Extract the data we need from the form, such as the form's action, method, and fields/values.
   */
  private function getFormData($formName, $html)
  {
    $data = array();

    $doc = new \DOMDocument();
    @$doc->loadHtml($html);
    $xpath = new \DOMXPath($doc);

    // Get the form, make sure it exists
    $form = $xpath->query('//form[@name = "' . $formName . '"]')->item(0);
    if ($form === null)
      throw new \Exception('Could not get the form data, form with name "' . $formName . '" was not found.');

    // Get the form's action and method attributes
    $data['action'] = $this->getAttributeValue('action', $form->attributes);
    $data['method'] = $this->getAttributeValue('method', $form->attributes);

    $fields = array();

    // Get all inputs
    $inputs = $xpath->query('//input[@type != "submit"]', $form);
    foreach ($inputs as $input)
    {
      $name = $this->getAttributeValue('name', $input->attributes);

      if ($this->getAttributeValue('type', $input->attributes) == 'checkbox')
        $value = $this->getAttributeValue('checked', $input->attributes);
      else
        $value = $this->getAttributeValue('value', $input->attributes);

      $fields[$name] = $value;
    }

    // Get all selects
    $selects = $xpath->query('//select', $form);
    foreach ($selects as $select)
    {
      $name = $this->getAttributeValue('name', $select->attributes);

      $selectedOption = $xpath->query('option[@selected]', $select)->item(0);
      if ($selectedOption !== null)
      {
        $value = $this->getAttributeValue('value', $selectedOption->attributes);
        $fields[$name] = $value;
      }
    }

    $data['fields'] = $fields;

    return $data;
  }

  /**
   * Submits the form to |$action| using HTTP method |$method| with the given |$fields|.
   * Returns the response from submitting the form.
   */
  private function submitForm($action, $method, $fields)
  {
    $this->request->setUrl($action);

    if ($method == 'get')
    {
      $this->request->setMethod(HTTP_METH_GET);
      $this->request->setQueryData($fields);
    }
    else
    {
      $this->request->setMethod(HTTP_METH_POST);
      $this->request->setPostFields($fields);
    }

    $this->request->send();

    return $this->request->getResponseBody();
  }
}
