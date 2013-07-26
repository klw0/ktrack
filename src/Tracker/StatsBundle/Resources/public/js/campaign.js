$(function() {

  setup();

  // Some stuff for the options button
  $('#options-button').click(function(e) {
    $(this).button('toggle');
    $('#options').fadeToggle(100);
    return false;
  });

  // Set up the date pickers
  //var datepickerOptions = {dateFormat: 'M d, yy'};
  //$("#filter_startDate").datepicker(datepickerOptions);
  //$("#filter_endDate").datepicker(datepickerOptions);

  var $filterForm = $('#filter-form');

  // Submit the form when the campaign selection changes
  $('#campaign_stats_filter_campaigns').change(function() {
    $filterForm.submit();
  });

  // Submit the form when the reload button is clicked
  var $reloadButton = $('#reload-button').click(function() {
    $filterForm.submit();
    return false;
  });

  // Keyboard shortcuts for reloading
  var reloadShortcut = function() {
    $reloadButton.click();
    return false;
  };

  // Submit the form when the campaign selection changes
  $('#filter_campaigns').change(reloadShortcut);

  // Keyboard shortcuts for reloading
  $(document).
    bind('keydown', 'ctrl+r', reloadShortcut).
    bind('keydown', 'command+r', reloadShortcut).
    bind('keydown', 'f5', reloadShortcut);

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

        // Enable tooltips, table sorting, etc. on the ajaxed HTML
        setup();

        // Change the reload button's state
        $reloadButton.button('reset');
      },
      dataType: 'html'
    });

    // Prevent the form from submitting normally
    return false;
  });

});

/**
 * Takes care of setting some things up on the page
 */
function setup()
{
  // Enable popovers
  $('[rel=popover]').popover();

  // Enable tooltips
  $('[rel=tooltip]').tooltip();

  enableSortingForAllTables();
}

/**
 * Enable sorting on all tables
 */
function enableSortingForAllTables()
{
  enableSorting('#target-stats');
  enableSorting('#landing-page-stats');
  enableSorting('#offer-stats');
  enableSorting('#campaign-stats');
}

/**
 * Enables sorting on the table
 */
function enableSorting(table)
{
  // Keep track of the sort lists for each table
  enableSorting.sortLists = enableSorting.sortLists || new Object;

  // If we don't have an existing sort list for this table, create a default one
  if (!(table in enableSorting.sortLists))
    enableSorting.sortLists[table] = [[0,0]];

  // Only enable sorting if the table has some rows
  if ($(table).find('tbody tr').length > 0) {
    // Enable table sorting, and use the stored sort list
    $(table).tablesorter({sortList: enableSorting.sortLists[table]});

    // Store any changes to the table's sort list
    $(table).bind('sortEnd', function(e) {
      enableSorting.sortLists[table] = e.target.config.sortList;
    });
  }
}
