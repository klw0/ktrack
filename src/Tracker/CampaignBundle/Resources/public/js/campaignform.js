$(function() {

  $("#campaign_type").change(function() {
    var $landingPageCollectionContainer = $("#landing-page-collection-container");
    var $addOfferGroupButton = $("#add-offer-group-button");

    if ($(this).val() == "direct_link")
    {
      // Hide the button to add more offer groups
      $addOfferGroupButton.hide();

      // Hide the landing pages
      $landingPageCollectionContainer.hide();
    }
    else
    {
      $addOfferGroupButton.show();
      $landingPageCollectionContainer.show();
    }
  });

  // Make sure that the forms are cleaned and validated
  $("form").submit(function() {
    cleanupTypeInstances("landing-page");
    cleanupTypeInstances("offer-group");
    cleanupTypeInstances("offer");

    // Validate?
  });

  $("#add-landing-page-button").click(function() {
    return addNewTypeInstance("landing-page", "landingPages", $(this));
  });

  $("#add-offer-group-button").click(function() {
    return addNewTypeInstance("offer-group", "offerGroups", $(this));
  });

  $("#offer-group-collection").on("click", ".add-offer-button", function(e) {
    return addNewTypeInstance("offer", "offers", $(this));
  });

  setupPage();
});

/*
 * Handles some basic page setup
 */
function setupPage() {
  // Trigger a change on |#campaign_type| so that landing pages are hidden or displayed
  $("#campaign_type").change();
}

/*
 * Removes additional empty type instances
 */
function cleanupTypeInstances(typeCssClass) {
  $("." + typeCssClass).each(function() {
    var empty = true;
    // look at each input
    $(this).find(":input").not("select").each(function () {
      // is there a value?
      if ($(this).val())
      {
        empty = false;
        return;
      }
    });

    // remove this item if none of the input fields have values
    if (empty)
      $(this).remove();
  });
}

/*
 * Generic function to add a new type instance
 */
function addNewTypeInstance(typeCssClass, typeId, $button) {
    var classSelector = "." + typeCssClass;
    var $collection = $button.parent();

    // grab the prototype template
    var prototype = $collection.data("prototype");

    // replace the "$$name$$" used in the id and name of the prototype with a number that's unique to our new type instance
    var count = $collection.children(classSelector).length;
    var re1 = new RegExp("\\[" + typeId + "\\]\\[\\$\\$name\\$\\$\\]", "g");
    var re2 = new RegExp(typeId + "_\\$\\$name\\$\\$", "g");
    var newTypeInstance = prototype.replace(re1, "[" + typeId + "][" + count + "]");
    newTypeInstance = newTypeInstance.replace(re2, typeId + "_" + count);

    // create a new type instance
    $button.before(newTypeInstance);

    return false;
}
