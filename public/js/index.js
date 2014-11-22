$(function() {
    function makeChart(chart_element, chart_data, chart_config) {
        var data = new google.visualization.DataTable();
        if (!google.visualization[chart_config.type]) {
            throw {
                name: 'InfosysError',
                message: 'No such graph type'
            };
        }

        for (var col in chart_config.columns) {
            if (chart_config.columns.hasOwnProperty(col)) {
                data.addColumn(chart_config.columns[col].type, chart_config.columns[col].name);
            }
        }
        data.addRows(chart_data);

        var options = {
          title: chart_config.title
        };

        var chart = new google.visualization[chart_config.type](chart_element);
        chart.draw(data, options);
    }


    $('.graph-link').click(function(e) {
        var self         = $(this),
            graph_dialog = $('<div></div>'),
            chart_div    = $('<div id="chart_div"></div>'),
            json;

        e.preventDefault();

        graph_dialog.append(chart_div);

        $.ajax({
            url: self.attr('href'),
            success: function(data) {
                graph_dialog.dialog({
                    modal: true,
                    height: 400,
                    width: 700,
                    close: function() {
                        graph_dialog.remove();
                    }
                }).append(chart_div);

                json = $.parseJSON(data);

                makeChart(chart_div[0], json.chart_data, json.chart_config);
            },
            error: function(jqXHR) {
                graph_dialog.remove();
                alert(jqXHR.responseText);
            }
        });
    });


});
