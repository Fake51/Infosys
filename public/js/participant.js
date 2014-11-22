$(function() {
    if ($.editable) {
        var reset = $.editable.types['defaults'].reset;

        function updateField(value, settings) {
            $(this).css('background-color', '#4AFE00').animate({'background-color': '#fff'}, 2000);
        }

        function updateError(settings, self) {
            reset.apply(this, [settings, self]);
            $(self).css('background-color', '#D00007').animate({'background-color': '#fff'}, 2000);
        }

        function checkInRequest(callback) {
            var date = new Date(),
                text = date.getFullYear() + '-0' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes();

            jQuery.ajax(window.infosys_data.checkin_url, {
                type: 'post',
                data: {action: 'mark-checkedin', user_id: window.participant.id},
                timeout: 3000,
                success: function(data) {
                    var message = $('<div class="alert alert-success">Checkin gennemført</div>');

                    $('.prev-next-buttons').first().before(message);

                    if (typeof callback === 'function') {
                        callback();
                    }

                    window.setTimeout(function () {
                        message.fadeOut(function () {
                            message.remove();
                        });
                    }, 3000);

                    $('td.checkin-time').text(text);
                },
                error: function(jqXHR) {
                    var message = $('<div class="alert alert-danger">Kunne ikke tjekke deltageren ind.<br/>' + jqXHR.responseText + '</div>');

                    $('.prev-next-buttons').first().before(message);

                    window.setTimeout(function () {
                        message.fadeOut(function () {
                            message.remove();
                        });
                    }, 3000);
                }
            });
        }

        function markPaymentMade(difference, callback) {
            var id        = window.participant.id,
                note_area = $('#paid_note'),
                date      = new Date(),
                text      = note_area.text() + '\n' + 'Deltageren har betalt ' + difference + ' ved checkin ' + date.getFullYear() + '-0' + (date.getMonth() + 1) + '-' + date.getDate();

            $.ajax({
                url: window.infosys_data.participant_editable_url,
                type: 'POST',
                data: {value: text.replace(/Click to edit/, ''), id: 'paid_note'},
                success: function () {
                    callback();

                    note_area.text(text);
                }
            });

        }

        function createPaymentDialog(difference, callback) {
            var dialog   = $('<div class="payment-dialog"></div>'),
                template = $('#checkin-template').text(),
                cover    = $('<div class="payment-cover"></div>'),
                handleDialogFinish = function () {
                    var self = $(this);

                    if (!self.hasClass('cancel')) {
                        markPaymentMade(difference, callback);
                    }

                    dialog.remove();
                    cover.remove();
                };

            dialog.html(template.replace(/:money:/, difference));

            cover.css('opacity', 0.3);

            dialog.on('click', 'a.btn', handleDialogFinish);

            $('body').append(cover).append(dialog);
        }

        function checkIn(callback) {
            var difference = parseInt($('td.difference').text(), 10);

            function makeCheckIn() {
                checkInRequest(callback);
            }

            if (difference) {
                createPaymentDialog(difference, makeCheckIn);
            } else {
                makeCheckIn();
            }
        }

        function checkInPrint() {
            checkIn(function () {
                window.open($('#character-sheet-link').attr('href'), '', '_blank');
            });
        }

        $('td.editable.text').editable(window.infosys_data.participant_editable_url, {
            submit: "Ok",
            cssclass: 'editable-input',
            indicator: 'Saving ...',
            tooltip: 'Click to edit',
            onerror: updateError
        });

        $('p.editable.textarea').editable(window.infosys_data.participant_editable_url, {
            submit: "Ok",
            cssclass: 'editable-input',
            type: 'textarea',
            indicator: 'Saving ...',
            tooltip: 'Click to edit',
            onerror: updateError
        });

        $('td.editable.yesno').editable(window.infosys_data.participant_editable_url, {
            type: 'select',
            data: '{"ja": "ja", "nej": "nej"}',
            submit: "Ok",
            indicator: 'Saving ...',
            tooltip: 'Click to edit',
            onerror: updateError
        });

        $('td.editable.gender').editable(window.infosys_data.participant_editable_url, {
            type: 'select',
            data: '{"m": "m", "k": "k"}',
            submit: "Ok",
            indicator: 'Saving ...',
            tooltip: 'Click to edit',
            onerror: updateError
        });

        $('td.editable.usertype').editable(window.infosys_data.participant_editable_url, {
            type: 'select',
            loadurl: window.infosys_data.ajax_get_user_types_url,
            submit: "Ok",
            indicator: 'Saving ...',
            tooltip: 'Click to edit',
            onerror: updateError
        });

        $('td.editable.clans').editable(window.infosys_data.participant_editable_url, {
            type: 'select',
            data: '{"nej": "nej", "Brujah": "Brujah", "Gangrel": "Gangrel", "Malkavian": "Malkavian", "Nosferatu": "Nosferatu", "Toreador": "Toreador", "Tremere": "Tremere", "Ventrue": "Ventrue"}',
            submit: "Ok",
            indicator: 'Saving ...',
            tooltip: 'Click to edit',
            onerror: updateError
        });

        function makeActivityDialogTable(source_row, dialog) {
            var new_table    = $('<table class="activity-dialog-table"><tbody><tr></tr></tbody></table>'),
                tbody        = new_table.find('tbody'),
                new_row      = tbody.find('tr'),
                source_table = source_row.closest('table'),
                activity_id,
                schedule_id,
                activity_name,
                priority,
                role,
                rows;

            source_table.find('thead').clone().insertBefore(tbody);
            tbody.appendTo(new_table);

            activity_name = source_row.find('.activity-name').text().trim();
            activity_id   = source_row.find('.activity-id').val();
            schedule_id   = source_row.find('.schedule-id').val();
            priority      = source_row.find('.priority').text();
            role          = source_row.find('.role').text();

            new_row.append('<td>' + activity_name + '</td><td><select name="schedule_id"></select></td><td><input type="number" min="1" max="4" value="' + priority + '" name="priority"></td><td><select name="role"><option value="spiller">spiller</option><option value="spilleder">spilleder</option></select></td><td><button class="delete-schedule">Slet</button></td>');

            new_row.find('select[name=role]').val(role);

            $.ajax({
                url: '/aktiviteter/ajax/activity-schedules/' + activity_id,
                type: 'GET',
                data: {schedule_id: schedule_id},
                success: function(data) {
                    var select = new_row.find('select[name=schedule_id]');
                    $($.parseJSON(data)).each(function(idx, item) {
                        $('<option value="' + item.id + '">' + item.text + '</option>').appendTo(select);
                    });

                    select.val(schedule_id);

                    dialog.dialog('open');
                }
            });

            dialog.on('click', 'button.update', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/deltager/ajax/updateschedule',
                    type: 'POST',
                    data: {participant_id: window.participant.id, old_schedule_id: schedule_id, old_priority: priority, schedule_id: tbody.find('select[name=schedule_id]').val(), priority: tbody.find('input[name=priority]').val(), activity_id: activity_id, role: tbody.find('select[name=role]').val()},
                    success: function(data) {
                        var json = $.parseJSON(data),
                            conflict_dialog = $('<div class="conflict-dialog"><ul></ul><button>Opdater</button></div>'),
                            list = conflict_dialog.find('ul');

                        if (json.status == 'conflict') {
                            $(json.schedules).each(function(idx, item) {
                                $('<li><input type="hidden" name="schedule_id" value="' + item.schedule_id + '"/>' + item.name + '</li>').appendTo(list);
                            });

                            list.sortable();

                            conflict_dialog.on('click', 'button', function() {
                                conflict_dialog.dialog('close');
                            });

                            conflict_dialog.dialog({
                                modal: true,
                                width: 500,
                                title: 'Sorter efter prioritet',
                                close: function() {
                                    var sorted = [];
                                    list.find('li input').each(function(idx, item) {
                                        sorted.push(item.value);
                                    });

                                    $.ajax({
                                        url: '/deltager/ajax/updateschedulepriorities',
                                        type: 'POST',
                                        data: {participant_id: window.participant.id, schedules: sorted},
                                        success: function() {
                                            dialog.remove();
                                            document.location = document.location.href;
                                        },
                                        error: function () {
                                        }
                                    });
                                }
                            });
                        } else {
                            dialog.remove();
                            document.location = document.location.href;
                        }
                    },
                    error: function(jqXHR) {
                        var json = $.parseJSON(jqXHR.responseText);
                        alert(json.message);
                    }
                });
            });

            new_row.find('button').click(function(e) {
                e.preventDefault();

                if (confirm('Er du sikker?')) {
                    $.ajax({
                        url: '/deltager/ajax/removeschedule',
                        type: 'POST',
                        data: {participant_id: window.participant.id, schedule_id: schedule_id},
                        success: function() {
                            source_row.remove();
                            dialog.remove();
                        },
                        error: function(jqXHR) {
                            alert('Kunne ikke slette tilmeldingen');
                        }
                    });
                }
            });

            return new_table;
        }

        $('.activity-signup').on('click', 'a', function(e) {
            e.stopPropagation();
        }).on('click', 'tbody tr', function(e) {
            var dialog = $('<div class="participant-activity-dialog"></div>'),
                updated = false;

            e.preventDefault();

            dialog.dialog({
                modal: true,
                width: 'auto',
                autoOpen: false,
                close: function() {
                    if (updated) {
                        document.location = document.location.href;
                    } else {
                        dialog.remove();
                    }
                }
            });

            dialog.append(makeActivityDialogTable($(this), dialog));
            dialog.append('<button class="update">Opdater</button>');
        });

        $('#checkin').click(checkIn);
        $('#checkin-print').click(checkInPrint);
    }

    if (document.location.href.search(/payment_edit/) != -1) {
        var search_form = $('form.search').clone();

        search_form.append('<input type="hidden" value="true" name="payment_edit"/>');
        $('.prev-next-buttons').append(search_form);
    }

    $('input.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        changeYear: true,
        yearRange: "1940:2013"
    });
});
