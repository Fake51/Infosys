"use strict";
(function($) {

  $(document).ready(function() {
    $('.confirm-button').click(function() {
      confirm(this);
    });

    $('.reject-button').click(function() {
      reject(this);
    });

    $('.group-button').click(function() {
      batchConfirm(this);
    });

    $('.manual-confirm-button').click(function() {
      manualConfirm(this);
    });
  });

  function confirm(button) {
    var transaction = button.getAttribute('transaction');
    var participant = button.getAttribute('participant');
    
    button.innerHTML = "Arbejder";
    button.disabled = true;
    $.ajax('/participant/ajax/confirm-mobilepay', {
      type: 'post',
      accepts: 'application/json',
      data: {list: [
        {
          transaction: transaction,
          participant: participant,
        }
      ]},
      success: function(data, status, jqXHR) {
        confirm_success(data, button);
      },
      error: function(jqXHR, status, error) {
        alert("Der skete en fejl:" + error);
        button.innerHTML = "Fejl";
        button.disabled = false;
      }
    });
  }

  function batchConfirm(button) {
    button.innerHTML = "Arbejder";
    button.disabled = true;

    var list = [];
    
    var tableid = button.id.replace('confirm-group-', '');
    $('table#'+tableid+' tr').each(function (index, element) {
      if(!element.classList.contains('top-row')) {
        list.push({
          transaction: element.getAttribute('transactionid'),
          participant: element.getAttribute('participantid'),
        })
      }
    });

    console.log(list);

    $.ajax('/participant/ajax/confirm-mobilepay', {
      type: 'post',
      accepts: 'application/json',
      data: {list: list},
      success: function(data, status, jqXHR) {
        confirm_success(data, button);
      },
      error: function(jqXHR, status, error) {
        alert("Der skete en fejl:" + error);
        button.innerHTML = "Fejl";
      }
    });
  }

  function manualConfirm(button) {
    var row = $(button).parents('tr');
    var transaction = row.attr('transactionid');
    var participant = row.find('input').val();

    button.innerHTML = "Arbejder";
    button.disabled = true;

    $.ajax('/participant/ajax/confirm-mobilepay', {
      type: 'post',
      accepts: 'application/json',
      data: {list: [
        {
          transaction: transaction,
          participant: participant,
        }
      ]},
      success: function(data, status, jqXHR) {
        confirm_success(data, button);
      },
      error: function(jqXHR, status, error) {
        alert("Der skete en fejl:" + error);
        button.innerHTML = "Fejl";
      }
    });
  }

  function confirm_success(data, button) {
    button.innerHTML = "Udført";
    for(var i = 0; i < data.length; i++){
      if(data[i].status.success) {
        moveToTable('processed', data[i].participant, data[i].transaction);
      } else {
        markError(data[i].participant, data[i].transaction, data[i].status.error);
      }
    }
    if (button.classList.contains('manual-confirm-button')) {
      for(var i = 0; i < data.length; i++){
        if(data[i].status.success) {
          updateRow(data[i].participant, data[i].transaction, data[i].status.info);
        }
      }
    }
  }

  function reject(button) {
    var transaction = button.getAttribute('transaction');
    var participant = button.getAttribute('participant');

    moveToTable('unknown', participant,transaction)
  }

  function moveToTable(tableid, participant,transaction) {
    var table = $('table#'+tableid)[0];
    
    // Create destination table if we don't have it
    if (table === undefined) {
      var category_list = $(".category-list")[0];

      var category_header = document.createElement('h3');
      if (tableid === "processed") {
        category_header.innerHTML = "Godkendte og registrerede betalinger";
        category_list.append(category_header);
      } else {
        category_header.innerHTML = "Afvist eller ingen match og skal håndteres manuelt";
        category_list.append(category_header);
        var paragraph = document.createElement('p');
        paragraph.innerHTML = "Du kan manuelt indtaste id på den deltager du vil bogføre betalingen hos";
        category_list.append(paragraph);
      }

      table = document.createElement('table');
      table.id = tableid;
      category_list.append(table);

      var table_header = $('thead')[0];
      table.prepend(table_header);

      var table_body = document.createElement('tbody');
      table.append(table_body);
    }
    var table_body = $(table).children('tbody');

    var pay_row = $('tr.top-row[transactionid='+transaction+']');
    var source_tbody = pay_row.parents('tbody');
    
    // Find and remove participant rows from source table
    var participant_row;
    var next_row = pay_row.next();
    while (!next_row.hasClass('top-row') && next_row.length !== 0) {
      if(next_row.attr("participantid") === participant) {
        // We want to keep the confirmed participant for later
        participant_row = next_row;
        participant_row.detach();
      } else {
        next_row.remove();
      }
      next_row = pay_row.next();
    }

    pay_row.detach();
    // If we're moving the transaction row to the unknown table we want to add some buttons for manual confirm
    if (tableid === "unknown") {
      // Input cell
      var cell = $('<td></td>');
      
      var label = $('<label>Deltager ID:</label>');
      label.attr('for', 'participant-'+participant);
      cell.append(label);

      var input = $('<input>');
      input.attr('id', 'participant-'+participant);
      input.attr('type', 'number');
      cell.append(input);
      pay_row.append(cell);

      // Button cell
      cell = $('<td></td>');
      
      var button = $('<button>Bogfør</button>');
      button.attr('id', 'manual-button-'+transaction);
      button.addClass('manual-confirm-button');
      button.click(function() {
        manualConfirm(this);
      });

      cell.append(button);
      pay_row.append(cell);
    } else {
      pay_row.find('label').remove();
      pay_row.find('input').remove();
      pay_row.find('button').remove();
    }
    table_body.append(pay_row);

    // Insert confirmed participant row in destination table
    if (tableid === "processed") {
      if (participant_row === undefined) {
        // We did a manual confirm and should create a row for the participant
        participant_row = $('<tr></tr>');
        participant_row.attr('participantid', participant);
        participant_row.attr('transactionid', transaction);
        participant_row.append($('<td>Deltager</td>'));
        participant_row.append($('<td>ID:'+participant+'</td>'));
      }
    
      table_body.append(participant_row);
      participant_row.find('button').remove();
    }

    // If table is empty, delete it and the related elemets
    if (source_tbody.children().length === 0){
      var source_table = source_tbody.parent('table');
      var next = source_table.next();
      while (next.prop('tagName') !== 'H3' && !next.hasClass('confirm-group-top') && next.length !== 0){
        next.remove();
        next = source_table.next();
      }

      var previous =source_table.prev();
      while (previous.prop('tagName') !== 'TABLE' && !previous.hasClass('spacer') && previous.length !== 0){
        previous.remove();
        previous = source_table.prev();
      }

      source_table.remove();
    }
  }

  function updateRow(participant, transaction, info) {
    var row = $('tr[participantid='+participant+'][transactionid='+transaction+']');
    row.empty();
    row.append('<td>Deltager</td>');
    row.append('<td>'+info['name']+'</td>');
    row.append('<td>'+info['phone']+'</td>');
    row.append('<td>'+info['signup-amount']+' / '+info['real-amount']+'</td>');
    row.append('<td></td>');
    row.append('<td>ID:'+info['display-id']+'</td>');

    // <td>Deltager</td>
    // <td><?=$participant['name']?></td>
    // <td><?=$participant['phone']?></td>
    // <td><?=$participant['signup-amount']?> / <?=$participant['real-amount']?></td>
    // <td><?=$participant['comment']?></td>
    // <td>ID:<?=$participant['display-id']?></td>

  }

}) (jQuery);