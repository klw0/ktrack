$(function() {
  // Change the update button's contents when clicked
  $('#update-btn').click(function() {
    $(this).button('loading');
  });

  // Disable the buttons and submit the form when the campaign selection changes
  $('#filter_campaigns').change(function() {
    $('.btn').addClass('disabled');

    $('#filter-form').submit();
  });

  // Sort it up, but only if the table actually has data
  if ($('#targets td').length > 0)
    $('#targets').tablesorter({ sortList: [[1,0], [2,1]] });
});
