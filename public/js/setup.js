/*global $ */

(function () {
    'use strict';

    var $fieldsets,
        $activeFieldset,
        $form,
        stop = function (e) {
            e.preventDefault();
            e.stopPropagation();
        },
        navigate = function (direction) {
            $activeFieldset.removeClass('active');

            if (direction === 'next') {
                $activeFieldset = $activeFieldset.next('fieldset');

            } else {
                $activeFieldset = $activeFieldset.prev('fieldset');
            }

            $activeFieldset.addClass('active');
        },
        makeDataObject = function () {
            var data = {};

            $activeFieldset.find('input:visible, select:visible').each(function () {
                var self = $(this);

                data[self.attr('name')] = self.val();
            });

            return data;
        },
        explainErrors = function (jqXHR) {
        },
        updateConfigPart = function (callback) {
            $.ajax({
                url: document.location.href + '?ajax=true',
                type: 'POST',
                data: makeDataObject(),
                success: callback,
                error: explainErrors
            });
        },
        nextPartHandler = function (e) {
            stop(e);

            updateConfigPart(function () {
                navigate('next');
            });
        },
        previousPartHandler = function (e) {
            stop(e);

            navigate('previous');
        },
        finishHandler = function (e) {
            stop(e);

            updateConfigPart(function () {
                document.location.reload();
            });
        },
        initialize = function () {
            $fieldsets = $('fieldset');
            $form      = $('form');

            $activeFieldset = $fieldsets.first().addClass('active');

            $form.on('click.next', 'button.next', nextPartHandler);
            $form.on('click.previous', 'button.previous', previousPartHandler);
            $form.on('click.finish', 'button.finish', finishHandler);
        };

    $(initialize);
}());
