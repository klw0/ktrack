$(function() {

  // TODO: Have this update only when conversions are recorded, if possible.  Use Server-Sent Events.
  // Update the revenue in the nav bar every 5 minutes
  $navbarRevenue = $('#navbar-revenue');
  setInterval(function() {
    $navbarRevenue.load($navbarRevenue.data('path'));
  }, 5 * 60 * 1000);
});
