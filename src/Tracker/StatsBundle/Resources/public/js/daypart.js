$(function() {

  var $filterForm = $('#filter-form');

  // Submit the form when the campaign selection changes
  $('#daypart_stats_filter_campaigns').change(function() {
    $filterForm.submit();
  });

  // Submit the form when the reload button is clicked
  var $reloadButton = $('#reload-button').click(function() {
    $filterForm.submit();
  });
 
  // On form submit...
  $filterForm.submit(function() {
    // Change the button's state
    $reloadButton.button('loading');

    // On submit, ajax POST the form to the appropriate url
    $.ajax({
      type: $filterForm.attr('method'),
      url: $filterForm.attr('action'),
      data: $filterForm.serialize(),
      success: function(data) {
        // Replace the content of |#stats| with the returned data
        $('#stats').html(data);

        // Change the reload button's state
        $reloadButton.button('reset');
      },
      dataType: 'html'
    });

    // Prevent the form from submitting normally
    return false;
  });

});
