$(function() {

  $('#trafficsource_serviceAuthenticationMethod').change(function() {
    var $apiKeyControlGroup = $('#trafficsource_serviceApiKey').closest('.control-group');
    var $usernameControlGroup = $('#trafficsource_serviceUsername').closest('.control-group');
    var $passwordControlGroup = $('#trafficsource_servicePassword').closest('.control-group');

    if ($(this).val() == 'api_key') {
      $apiKeyControlGroup.show();
      $usernameControlGroup.hide();
      $passwordControlGroup.hide();
    }
    else {
      $apiKeyControlGroup.hide();
      $usernameControlGroup.show();
      $passwordControlGroup.show();
    }
  });

  setup();

});

/**
 * Set up the form
 */
function setup()
{
  // Trigger a change on |#trafficsource_serviceAuthenticationMethod| so that the correct fields are shown or hidden
  $('#trafficsource_serviceAuthenticationMethod').change();
}
