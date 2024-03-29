"use strict";
(function(){
  let selected;

  $( document ).ready(function() {
    $('td.buttons img').on('click', function(){
      moveRow($(this).parents('tr'), this.getAttribute('direction'));
    });

    $('tr').on('click', function(){
      if (selected) selected.removeClass('selected');
      selected = $(this);
      selected.addClass('selected');
    });

    $(document).on('keydown', function(evt){
      if(!selected) return;
      if(evt.key === 'ArrowUp'){
        evt.preventDefault();
        moveRow(selected, 'up');
      }
      if(evt.key === 'ArrowDown') {
        evt.preventDefault();
        moveRow(selected, 'down');
      }
    });
  });

  function moveRow(row, direction) {
    //console.log("Moving row:", row, "Direction:", direction);
    if (row[0]) row = row[0];
    if (selected) selected.removeClass('selected');
    selected = $(row);
    selected.addClass('selected');

    let switch_row;
    if (direction === 'up') {
      switch_row = row.previousElementSibling;
    }
    if (direction === 'down') {
      switch_row = row.nextElementSibling;
    }
    // If there is no row in that direction, don't do anything
    if (!switch_row) return;

    $.ajax({
      url: infosys_data.show_wear_ajax_url,
      method: 'POST',
      data: {
        action: 'switch_row',
        source_row: row.getAttribute('row-id'),
        destination_row: switch_row.getAttribute('row-id'),
      },
      success: function(response) {
        console.log(response);
        doMove();
      },
      error: function(response) {
        console.log("Error:", response);
      },
    });

    function doMove() {
      let temp_row_order = row.getAttribute('row-order');
      row.setAttribute('row-order', switch_row.getAttribute('row-order'));
      switch_row.setAttribute('row-order', temp_row_order);

      if (direction === 'up') {
        $(row).detach().insertBefore(switch_row);
      }
      if (direction === 'down') {
        $(row).detach().insertAfter(switch_row);
      }
    }
  }

})();