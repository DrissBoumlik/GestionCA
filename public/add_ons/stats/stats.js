$(function () {
    var dates = undefined;

    $.ajax({
        url: 'getDates',
        method: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (response) {
            let data = response.dates;
            $('.tree-view').each(function (index, item) {
                new Tree('#' + $(this).attr('id'), {
                    data: [{id: '-1', text: 'Dates', children: data}],
                    closeDepth: 2,
                    loaded: function () {
                        // this.values = ['0-0-0', '0-1-1', '0-0-2'];
                        console.log(this.selectedNodes);
                        console.log(this.values);
                        // this.disables = ['0-0-0', '0-0-1', '0-0-2']
                    },
                    onChange: function () {
                        dates = this.values;
                    }
                });
                // $(this).find('.treejs-switcher').first().parent().first().addClass('treejs-node__close')
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });

    let statsRegions = {element_dt: undefined, element: $('#statsRegions'), columns: undefined};
    let statsCodes = {element_dt: undefined, element: $('#statsCodes'), columns: undefined};


    getColumns('getRegionsColumn', 'getRegions', statsRegions);

    // getColumns('getNonValidatedFoldersColumn', 'getNonValidatedFolders', stats, stats_dt);

    function getColumns(routeColumns, route, object) {
        $.ajax({
            url: routeColumns,
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (response) {
                object.columns = response.columns;
                object.element_dt = InitDataTable(object, route)
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('error');
            }
        });
    }

    $('#refresh').on('click', function () {
        statsRegions.element_dt = InitDataTable(statsRegions, 'getRegions', {dates});
    });

    function InitDataTable(object, route, data = null) {
        if ($.fn.DataTable.isDataTable(object.element_dt)) {
            object.element_dt.destroy();
            console.log($.fn.DataTable.isDataTable(object.element_dt));
        }
        console.log(object);
        return object.element.DataTable({
            responsive: true,
            info: false,
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: route,
                data: data,
            },
            columns: object.columns
        });
    }

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }
});
