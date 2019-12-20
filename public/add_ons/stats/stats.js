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
    let statsRegionsChart = {elementChart: undefined, element_id: 'statsRegionsChart', data: undefined};
    getColumns('getRegionsColumn', 'getRegions', statsRegions, statsRegionsChart);
    $('#refreshRegions').on('click', function () {
        statsRegions.element_dt = InitDataTable(statsRegions, 'getRegions', {dates});
    });


    let statsFoldersByType = {element_dt: undefined, element: $('#statsTypes'), columns: undefined};
    getColumns('getNonValidatedFoldersColumn/Code_Type_Intervention', 'getNonValidatedFolders/Code_Type_Intervention', statsFoldersByType);


    let statsFoldersByCode = {element_dt: undefined, element: $('#statsCodes'), columns: undefined};
    getColumns('getNonValidatedFoldersColumn/Code_Intervention', 'getNonValidatedFolders/Code_Intervention', statsFoldersByCode);

    let statscallsPos = {element_dt: undefined, element: $('#statsCallsPos'), columns: undefined};
    getColumns('getClientsByCallStateColumn/Joignable', 'getClientsByCallState/Joignable', statscallsPos);

    let statscallsNeg = {element_dt: undefined, element: $('#statsCallsNeg'), columns: undefined};
    getColumns('getClientsByCallStateColumn/Injoignable', 'getClientsByCallState/Injoignable', statscallsNeg);

    // getColumns('getNonValidatedFoldersColumn', 'getNonValidatedFolders', stats, stats_dt);

    function getColumns(routeColumns, route, object, objectChart = null) {
        $.ajax({
            url: routeColumns,
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (response) {
                object.columns = response.columns;
                object.element_dt = InitDataTable(object, route);
                console.log(objectChart);
                if (objectChart !== null) {
                    console.log(response.data);
                    response.data.map(function (item) {
                        console.log(item.regions);
                    });
                    let chartElement = document.getElementById(objectChart.element_id);
                    let labels = response.columns;
                    labels.pop();
                    labels.shift();
                    let chart = new Chart(chartElement, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: response.data
                        }


                    });
                }


            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('error');
            }
        });
    }

    $('#refreshFoldersByType').on('click', function () {
        statsFoldersByType.element_dt = InitDataTable(statsFoldersByType, 'getNonValidatedFolders/Code_Type_Intervention', {dates});
    });
    $('#refreshFoldersByCode').on('click', function () {
        statsFoldersByCode.element_dt = InitDataTable(statsFoldersByCode, 'getNonValidatedFolders/Code_Intervention', {dates});
    });


    $('#refreshCallResultPos').on('click', function () {
        statscallsPos.element_dt = InitDataTable(statscallsPos, 'getClientsByCallState/Joignable', {dates});
    });
    $('#refreshCallResultNeg').on('click', function () {
        statscallsNeg.element_dt = InitDataTable(statscallsNeg, 'getClientsByCallState/Injoignable', {dates});
    });


    function InitDataTable(object, route, data = null) {
        if ($.fn.DataTable.isDataTable(object.element_dt)) {
            object.element_dt.destroy();
        }
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
