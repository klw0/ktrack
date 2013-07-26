<?php

namespace Tracker\TrafficSourceBundle\Wrapper;

/**
 * This class wraps the the details of the LeadImpact scraping methods.
 */
class LeadImpact implements TrafficSourceWrapperInterface
{
  const BASE_URL = 'https://secure.leadimpact.com/';
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
   * Returns an array of target data pulled from LeadImpact. Each target is a hash of the form:
   *   {name: x, active: y, currentBid: z, creativeIdentifier: q}
   */
  public function getTargetDetailsForCreative($creativeId)
  {
    // Log into LI so that that |$request| gets filled with the proper cookies and whatever else LI needs.
    $this->login($this->username, $this->password);

    $creativeUrl = self::BASE_URL . 'campaign/keywords.aspx?qs=' . $creativeId;

    // Get the default page for the creative
    $this->request->setUrl($creativeUrl);
    $this->request->setMethod(HTTP_METH_GET);
    $this->request->send();
    $response = $this->request->getResponseBody();

    // How many pages are there?
    $doc = new \DOMDocument();
    @$doc->loadHtml($response);
    $xpath = new \DOMXPath($doc);
    $numPages = $xpath->query('//tr[@class = "GridPager"]//span[text() = "Page"]/../select/option')->length;

    $targets = array();
    for ($i = 1; $i <= $numPages; $i++)
    {
      // Only get the response if page > 1, because we already have page 1 in |$response|
      if ($i > 1)
      {
        $formData = $this->getFormData('aspnetForm', $response);

        // Get page $i
        $formFieldOverrides = array(
          'ctl00$phMainContent$grid$grid$ctl503$Pager$ddlPageIndex' => $i,
        );

        $formFields = array_merge($formData['fields'], $formFieldOverrides);
        $response = $this->submitForm(self::BASE_URL . 'campaign/' . $formData['action'], $formData['method'], $formFields);
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
    $formData = $this->getFormData('aspnetForm', $this->request->getResponseBody());

    $formFieldOverrides = array(
      'ctl00$phMainContent$txtUserID'   => $username,
      'ctl00$phMainContent$txtPassword' => $password,
      'ctl00$phMainContent$btnLogin'    => 'Login',
    );

    $formFields = array_merge($formData['fields'], $formFieldOverrides);
    $response = $this->submitForm(self::BASE_URL . $formData['action'], $formData['method'], $formFields);

    if (strpos($response, 'Invalid Credentials') !== FALSE)
      throw new \Exception('Could not log in to LeadImpact, invalid credentials.');
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
  private function getFormData($formId, $html)
  {
    $data = array();

    $doc = new \DOMDocument();
    @$doc->loadHtml($html);
    $xpath = new \DOMXPath($doc);

    // Get the form, make sure it exists
    $form = $xpath->query('//form[@id = "' . $formId . '"]')->item(0);
    if ($form === null)
      throw new \Exception('Could not get the form data, form with id "' . $formId . '" was not found.');

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

  /**
   * Gets target data from the given HTML.
   */
  private function getTargetData($html, $creativeId)
  {
    $doc = new \DOMDocument();
    @$doc->loadHtml($html);

    $xpath = new \DOMXPath($doc);
    $rows = $xpath->query('//tr[@class = "GridRow" or @class = "GridAlternate"]');

    $targets = array();
    foreach ($rows as $row)
    {
      // Name of the target is in the second column
      $name = $xpath->query('td[2]/span', $row)->item(0)->textContent;

      // Status of the target is in the sixth column
      $active = ($xpath->query('td[6]/span', $row)->item(0)->textContent == 'Active');

      // The current bid/max bid is in the fourth column
      $currentBid = $xpath->query('td[4]/input/@value', $row)->item(0)->value;

      $targets[] = array(
        'name'                => strtolower($name),
        'active'              => $active,
        'currentBid'          => $currentBid,
        'creativeIdentifier'  => $creativeId,
      );
    }

    return $targets;
  }
}
