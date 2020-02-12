$(function () {
    let dates = undefined;
    let agent_name = '';
    let agence_code = '';
    let ajaxRequests = 0;


    const agence_name_element = $('#agence_name');
    if (agence_name_element) {
        if (agence_name_element.val()) {
            agence_code = agence_name_element.val();
        }
    }
    const agent_name_element = $('#agent_name');
    if (agent_name_element) {
        if (agent_name_element.val()) {
            agent_name = agent_name_element.val();
        }
    }
    const params = window.location.href.split('?')[1];
    if (params) {
        const paramsList = params.split('&');
        for (let param of paramsList) {
            const p = param.split('=');
            if (p[0] === 'agence_code') {
                agence_code = p[1];
            }
            if (p[0] === 'agent_name') {
                agent_name = p[1];
            }
        }
    }

    const filterData = () => {
        // console.log(agence_code, agent_name);
        return {
            // dates,
            // resultatAppel,
            // gpmtAppelPre,
            // codeTypeIntervention,
            // codeIntervention,
            // codeRdvIntervention,
            // codeRdvInterventionConfirm,
            // groupement,
            // nomRegion,
            agent_name,
            agence_code
        };
    };

    let userObject = {
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-01', rows: ''},
    };

    //<editor-fold desc="SELECTED FILTER">
    let statsCallsCloture = {
        columnName: 'Nom_Region',
        rowName: 'Resultat_Appel',
        element_dt: undefined,
        element: 'statsCallsCloture',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-1', rows: '#stats-callResult-filter'},
        filterQuery: {
            queryJoin: ' and Resultat_Appel not like "=%" and Groupement not like "Non Renseigné" and Groupement not like "Appels post"',
            subGroupBy: ' GROUP BY Id_Externe, Nom_Region, Groupement, Key_Groupement, Resultat_Appel) groupedst ',
            queryGroupBy: 'group by st.Id_Externe, Nom_Region, Groupement, Key_Groupement, Resultat_Appel'
        },
        routeCol: 'regions/details/groupement/columns?key_groupement=Appels-clture',
        routeData: 'regions/details/groupement?key_groupement=Appels-clture',
        objChart: {
            element_chart: undefined,
            element_id: 'statsCallsClotureChart',
            data: undefined,
            chartTitle: 'Type Résultats Appels'
        }
    };
    if (elementExists(statsCallsCloture)) {
        getColumns(statsCallsCloture, filterData(), {
            removeTotal: false,
            refreshMode: false,
            removeTotalColumn: false,
            details: false,
            pagination: false
        });
        $('#refreshCallsCloture').on('click', function () {
            toggleLoader($('#refreshAll').parents('.col-12'));
            getColumns(statsCallsCloture, filterData(), {
                removeTotal: false,
                refreshMode: true,
                removeTotalColumn: false,
                details: false,
                pagination: false
            });
        });
    }
    //</editor-fold>

    //<editor-fold desc="FOLDERS CODE / TYPE">
    let statsFoldersByType = {
        columnName: 'Nom_Region',
        rowName: 'Code_Type_Intervention',
        element_dt: undefined,
        element: 'statsFoldersByType',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-6', rows: '#code-type-intervention-filter'},
        filterQuery: {
            queryJoin: ' and Groupement like "Appels clôture" ',
            subGroupBy: ' GROUP BY Id_Externe, Nom_Region, Code_Type_Intervention , Resultat_Appel) groupedst ',
            queryGroupBy: ' GROUP BY st.Id_Externe,Nom_Region, Code_Type_Intervention , Resultat_Appel'
        },
        routeCol: 'nonValidatedFolders/columns/Code_Type_Intervention',
        routeData: 'nonValidatedFolders/Code_Type_Intervention',
        objChart: {
            element_chart: undefined,
            element_id: 'statsFoldersByTypeChart',
            data: undefined,
            chartTitle: 'Répartition des dossiers non validés par Code Type intervention'
        }
    };
    if (elementExists(statsFoldersByType)) {
        getColumns(statsFoldersByType, filterData());
        $('#refreshFoldersByType').on('click', function () {
            toggleLoader($('#refreshAll').parents('.col-12'));
            getColumns(statsFoldersByType, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: false,
                removeTotalColumn: false,
                pagination: false
            });
        });
    }

    let statsFoldersByCode = {
        columnName: 'Nom_Region',
        rowName: 'Code_Intervention',
        element_dt: undefined,
        element: 'statsFoldersByCode',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-7', rows: '#code-intervention-filter'},
        filterQuery: {
            queryJoin: ' and Groupement like "Appels clôture" ',
            subGroupBy: ' GROUP BY Id_Externe, Nom_Region, Code_Intervention , Resultat_Appel) groupedst ',
            queryGroupBy: ' GROUP BY st.Id_Externe,Nom_Region, Code_Intervention , Resultat_Appel'
        },
        routeCol: 'nonValidatedFolders/columns/Code_Intervention?key_groupement=Appels-clture',
        routeData: 'nonValidatedFolders/Code_Intervention?key_groupement=Appels-clture',
        objChart: {
            element_chart: undefined,
            element_id: 'statsFoldersByCodeChart',
            data: undefined,
            chartTitle: 'Répartition des dossiers non validés par code intervention'
        }
    };
    if (elementExists(statsFoldersByCode)) {
        getColumns(statsFoldersByCode, filterData());
        $('#refreshFoldersByCode').on('click', function () {
            toggleLoader($('#refreshAll').parents('.col-12'));
            getColumns(statsFoldersByCode, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: false,
                removeTotalColumn: false,
                pagination: false
            });
        });
    }
    //</editor-fold>

    //<editor-fold desc="ALL STATS">
    let statsColturetech = {
        element_dt: undefined,
        element: 'statsColturetech',
        columnName: 'Nom_Region',
        rowName: '',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-02', rows: ''},
        filterQuery: {
            appCltquery: true,
        },
        routeCol: 'Cloturetech/columns?key_groupement=Appels-clture',
        routeData: 'Cloturetech?key_groupement=Appels-clture',
        objChart: {
            element_chart: undefined,
            element_id: 'statsColturetechChart',
            data: undefined,
            chartTitle: 'Délai de validation post solde'
        }
    };
    if (elementExists(statsColturetech)) {
        getColumns(statsColturetech, filterData(), {
            removeTotal: false,
            refreshMode: false,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        $('#refreshColturetech').on('click', function () {
            toggleLoader($('#refreshAll').parents('.col-12'));
            getColumns(statsColturetech, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: false,
                removeTotalColumn: false,
                pagination: false
            });
        });
    }

    let statsGlobalDelay = {
        element_dt: undefined,
        element: 'statsGlobalDelay',
        columnName: 'Nom_Region',
        rowName: '',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-03', rows: ''},
        filterQuery: {
            queryJoin: '',
            queryGroupBy: ' GROUP BY st.Id_Externe'
        },
        routeCol: 'GlobalDelay/columns?key_groupement=Appels-clture',
        routeData: 'GlobalDelay?key_groupement=Appels-clture',
        objChart: {
            element_chart: undefined,
            element_id: 'statsGlobalDelayChart',
            data: undefined,
            chartTitle: 'Délai global de traitement OT'
        }
    };
    if (elementExists(statsGlobalDelay)) {
        getColumns(statsGlobalDelay, filterData(), {
            removeTotal: false,
            refreshMode: false,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        $('#refreshGlobalDelay').on('click', function () {
            toggleLoader($('#refreshAll').parents('.col-12'));
            getColumns(statsGlobalDelay, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: false,
                removeTotalColumn: false,
                pagination: false
            });
        });
    }
    //</editor-fold>

    let globalElements = [userObject, statsCallsCloture, statsFoldersByType, statsFoldersByCode, statsColturetech, statsGlobalDelay];

    let detailClick = false;

    getDatesFilter();

    userFilter();

    //<editor-fold desc="FUNCTIONS">
    function getColumns(object, data = null, params = {
        removeTotal: true,
        refreshMode: false,
        details: false,
        removeTotalColumn: false,
        pagination: false
    }) {
        ajaxRequests++;
        if (params.refreshMode) {
            data = {...data, refreshMode: true};
        }
        if (object.filterTree && object.filterTree.rows) {
            data = {...data, 'rowFilter': object.filterTree.rows};
        }
        if (object.filterTree) {
            data = {...data, 'dates': object.filterTree.dates};
        }

        toggleLoader($('#' + object.element).parents('.col-12'));

        $.ajax({
            url: APP_URL + '/' + object.routeCol,
            method: 'GET',
            data: data,
            success: function (response) {
                if (object.filterElement) {
                    object.filterTree.dates = response.filter ? response.filter.date_filter : [];
                    if (object.filterTree.datesTreeObject && object.filterTree.dates) {
                        object.filterTree.datesTreeObject.values = object.filterTree.dates;
                        if (object.objDetail) {
                            object.objDetail.filterTree.dates = object.filterTree.dates;
                        }
                    }
                    if (response.rows && response.rows.length) {
                        let rowsFilterData = response.rows.map(function (d, index) {
                            return {
                                id: d,
                                text: d
                            };
                        });
                        new Tree(object.filterElement.rows, {
                            data: [{id: '-1', text: response.rowsFilterHeader, children: rowsFilterData}],
                            closeDepth: 1,
                            loaded: function () {
                                if (response.filter && response.filter.rows_filter) {
                                    this.values = object.filterTree.rows = response.filter.rows_filter;
                                    // console.log(this.values);
                                }
                            },
                            onChange: function () {
                                object.filterTree.rows = this.values;
                                // console.log(this.values);
                            }
                        });
                    }

                    let filters = response.filter;
                    if (filters !== null && filters !== undefined) {
                        object.filterTree.dates = filters.date_filter;
                    }
                }

                if (response.columns.length) {
                    let reformattedColumns = [...response.columns].map(function (column) {

                        return {
                            ...column,
                            render: function (data, type, full, meta) {
                                let newData = data;
                                if (newData !== null) {
                                    newData = newData.toString();
                                    if (newData.indexOf('/') !== -1) {
                                        newData = newData.split('/').join('<br/>');
                                        // newData = newData[0] + '<br/>' + newData[1];
                                    }
                                } else {
                                    newData = '';
                                }
                                let classHasTotalCol = params.removeTotalColumn ? 'hasTotal' : '';

                                let rowClass = full.isTotal ? '' : 'pointer detail-data';
                                return '<span class="' + rowClass + ' ' + classHasTotalCol + '">' + newData + '<\span>';
                            }
                        };
                    });

                    // object.columns = [...response.columns];
                    object.columns = [...reformattedColumns];
                } else {
                    object.columns = [{title: 'Résultats'}];
                }

                if (data !== null && data !== undefined) {
                    try {
                        object.element_dt = InitDataTable(object, data, {
                            removeTotal: params.removeTotal,
                            removeTotalColumn: params.removeTotalColumn,
                            details: params.details,
                            pagination: params.pagination
                        });
                        if (params.details) {
                            $('#' + object.element).on('click', 'td.details-control', function () {
                                const tr = $(this).closest('tr');
                                const row = object.element_dt.row(tr);
                                if (row.child.isShown()) {
                                    // This row is already open - close it
                                    destroyChild(row);
                                    tr.removeClass('shown');
                                } else {
                                    // Open this row
                                    data = {...data, key_groupement: tr.find('td:nth-child(2)').text()};
                                    object.objDetail.element = 'details-' + $('tr').index(tr);
                                    createChild(row, object, data); // class is for background colour
                                    tr.addClass('shown');
                                }
                            });
                        }
                        // CELL CLICK
                        let tableId = '#' + object.element;
                        $(tableId + ' tbody').on('click', 'td', function () {
                            let agent_name = $('#agent_name').val();
                            let agence_name = $('#agence_name').val();
                            let col = object.element_dt.cell(this).index().column + 1;
                            let row = object.element_dt.cell(this).index().row + 1;
                            let colText = $(tableId + " thead th:nth-child(" + col + ")").text();
                            let rowText = $(tableId + " tbody tr:nth-child(" + row + ") td:" + (params.details ? "nth-child(2)" : "first-child")).text();
                            if(object.element === 'statsColturetech'){
                                switch (rowText) {
                                    case 'superieur d\'un jour': rowText = ' and TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_SOLDE,EXPORT_ALL_Date_VALIDATION) > 1440'; break;
                                    case 'Entre 1H et 6h' : rowText = ' and TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_SOLDE,EXPORT_ALL_Date_VALIDATION) between 60 and 360 '; break;
                                    case 'Entre 30min et 1H': rowText = ' and TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_SOLDE,EXPORT_ALL_Date_VALIDATION) BETWEEN 30 and 60'; break;
                                    default: rowText = ' and TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_SOLDE,EXPORT_ALL_Date_VALIDATION) > 60';
                                }
                            }
                            if (object.columnName === 'Date_Heure_Note_Semaine') {
                                colText = colText.split('_')[0];
                            }
                            let lastRowIndex = object.element_dt.rows().count();
                            let lastColumnIndex = object.element_dt.columns().count();

                            if (((params.details && col > 2) || (!params.details && col > 1))
                                && ((params.removeTotal && row < lastRowIndex) || (!params.removeTotal && row <= lastRowIndex))
                                && ((params.removeTotalColumn && col < lastColumnIndex) || (!params.removeTotalColumn && col <= lastColumnIndex))) {
                                let dates = object.filterTree.dates;
                                window.location = APP_URL + '/all-stats?' +
                                    'row=' + (object.rowName === undefined || object.rowName === null ? '' : object.rowName) +
                                    '&rowValue=' + rowText +
                                    '&col=' + (object.columnName === undefined || object.columnName === null ? '' : object.columnName)+
                                    '&colValue=' + colText +
                                    '&agent=' + (agent_name === undefined || agent_name === null ? '' : agent_name) +
                                    '&agence=' + (agence_name === undefined || agence_name === null ? '' : agence_name) +
                                    '&dates=' + (dates === undefined || dates === null ? '' : dates) +
                                    '&queryJoin=' + (object.filterQuery.queryJoin === undefined || object.filterQuery.queryJoin === null ? '' : object.filterQuery.queryJoin) +
                                    '&subGroupBy=' + (object.filterQuery.subGroupBy === undefined || object.filterQuery.subGroupBy === null ? '' : object.filterQuery.subGroupBy) +
                                    '&queryGroupBy=' + (object.filterQuery.queryGroupBy === undefined || object.filterQuery.queryGroupBy === null ? '' : object.filterQuery.queryGroupBy) +
                                    '&appCltquery' + (object.filterQuery.appCltquery === undefined || object.filterQuery.appCltquery === null ? '' : object.filterQuery.appCltquery) +
                                    (object.routeData.includes('nonValidatedFolders') ? '&Resultat_Appel=Appels clôture - CRI non conforme' : '');
                            }
                            // console.log(colText + ' --- ' + rowText)
                        });
                    } catch (error) {
                        console.log(error);
                        // swal(
                        //     'Error!',
                        //     "Aucun résultat n'a été trouvé",
                        //     'error'
                        // );
                        Swal.fire({
                            // position: 'top-end',
                            type: 'error',
                            title: "Vous devez actualiser la page",
                            showConfirmButton: true,
                            customClass: {
                                confirmButton: 'btn btn-success m-1',
                            },
                            confirmButtonText: 'Ok',
                        });
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                console.log(APP_URL + '/' + object.routeCol);
            }
        });
    }

    function InitDataTable(object, data = null, params = {
        removeTotal: true,
        removeTotalColumn: false,
        details: false,
        pagination: false
    }) {
        let table = $('#' + object.element);
        if ($.fn.DataTable.isDataTable(table)) {
            table.off('click', 'td.details-control');
            table.DataTable().destroy();
            let tableID = object.element;
            let tableParent = table.parents('.card-body');
            table.remove();
            $('#' + tableID + '_wrapper').remove();
            let newTable = object.columns.reduce(function (accumulator, current) {
                return accumulator + '<th>' + current.title + '</th>';
            }, params.details ? '<th></th>' : '');

            newTable = '<table id="' + tableID + '" class="table table-bordered table-striped table-valign-middle capitalize">' +
                '<thead>' + newTable + '</thead><tbody></tbody></table>';
            tableParent.append(newTable);
            table = $('#' + object.element);
        }

        if (params.details) {
            object.objDetail.columns = [...object.columns];
            object.objDetail.columns = object.objDetail.columns.map(function (item, index) {
                if (index === 0) {
                    return {...item, data: 'Resultat_Appel', name: 'Resultat_Appel', title: 'Resultat Appel'};
                }
                return {...item, title: item.name};
            });

            object.columns.unshift({
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '',
                width: '10%'
            });
        }

        return table.DataTable({
            destroy: true,
            language: frLang,
            responsive: true,
            info: false,
            processing: true,
            serverSide: true,
            searching: false,
            // ordering: false,
            bPaginate: params.pagination,
            ajax: {
                url: APP_URL + '/' + object.routeData,
                data: data,
            },
            columns: object.columns,
            initComplete: function (settings, response) {
                ajaxRequests--;
                object.data = [...response.data];
                if (ajaxRequests === 0) {
                    toggleLoader($('#refreshAll').parents('.col-12'), true);
                }
                if (object.objChart !== null && object.objChart !== undefined) {
                    try {
                        InitChart(object.objChart, object.columns, response.data, {
                            removeTotal: params.removeTotal,
                            removeTotalColumn: params.removeTotalColumn,
                            details: params.details
                        });
                        let parent = $('#' + object.element).parents('.col-12');
                        toggleLoader(parent, true);
                    } catch (error) {
                        console.log(error);
                    }
                }
            }
        });
    }

    function InitChart(objectChart, columns, data, params = {
        removeTotal: true,
        removeTotalColumn: false,
        details: false
    }) {
        // console.log(objectChart.chartTitle);
        // console.log(columns);
        // console.log(data);
        let labels = [...columns];
        labels = labels.map((column) => {
            return column.data;
        });
        if (params.details) {
            labels.shift();
        }
        let column = labels.shift();
        if (params.removeTotalColumn) {
            labels.pop();
        }
        let datasets = [...data];
        if (params.removeTotal) {
            datasets.pop();
        }
        let uniqueColors = [];
        datasets = datasets.map((item) => {
            let regions = item.values.map((value) => {
                return parseFloat(isNaN(value) ? value.replace('%', '') : value);
            });
            let _dataItem = {label: item[column], backgroundColor: dynamicColors(uniqueColors), data: regions};
            // let _dataItem = {label: item[column], backgroundColor: dynamicColors(uniqueColors), data: regions, fill: false, borderColor: dynamicColors(uniqueColors)};
            return _dataItem;
        });

        let chartID = objectChart.element_id;
        let chart = $('#' + chartID);

        if (!detailClick) {
            let chartParent = chart.parents('.col-12');
            chartParent.children('.chartjs-size-monitor').remove();
            chart.remove();
            let newChart = '<canvas id="' + chartID + '">';
            chartParent.append(newChart);
        } else {
            detailClick = false;
        }

        var ctx = document.getElementById(objectChart.element_id).getContext('2d');
        let ChartData = {labels, datasets};
        // if (objectChart.element_chart !== null && objectChart.element_chart !== undefined) {
        //     objectChart.element_chart.destroy();
        // }
        objectChart.element_chart = new Chart(ctx, {
            type: 'bar',
            data: ChartData,
            options: {
                title: {
                    display: true,
                    text: objectChart.chartTitle
                },
                tooltips: {
                    mode: 'index',
                    intersect: true
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        stacked: false,
                    }],
                    yAxes: [{
                        stacked: false
                    }]
                },
                plugins: {
                    // Change options for ALL labels of THIS CHART
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        font: {
                            weight: 'bold',
                            // size: 14
                        },
                        rotation: -45,
                        display: function (context) {
                            // if(context.dataset.data.length > 10) {
                            //     return false;
                            // }
                            return context.dataset.data[context.dataIndex] !== 0;
                        }
                    }
                }
            }
        });
    }

    function getDatesFilter() {
        $.ajax({
            url: APP_URL + '/dates',
            method: 'GET',
            success: function (response) {
                let treeData = response.dates;

                $('.tree-view').each(function (index, item) {
                    let treeId = '#' + $(this).attr('id');
                    let object = globalElements.filter(function (element) {
                        return element.filterElement.dates === treeId;
                    });
                    new Tree(treeId, {
                        data: [{id: '-1', text: 'Dates', children: treeData}],
                        closeDepth: 1,
                        loaded: function () {
                            // this.values = ['2019-12-02', '2019-12-03'];
                            // console.log(this.selectedNodes);
                            // console.log(this.values);
                            // this.disables = ['0-0-0', '0-0-1', '0-0-2']

                            if (object.length) {
                                object = object[0];
                                object.filterTree.datesTreeObject = this;
                                if (object.filterTree.dates) {
                                    object.filterTree.datesTreeObject.values = object.filterTree.dates;
                                }
                            }
                        },
                        onChange: function () {
                            dates = this.values;
                            if (object.filterTree) {
                                object.filterTree.dates = this.values;
                            }
                        }
                    });
                });
                // if (datesFilterListExist && datesFilterValuesExist) {
                //     assignFilter(datesFilterList, datesFilterValues);
                // }
                $('.treejs-node .treejs-nodes .treejs-switcher').click();
                $('.refresh-form button').removeClass('d-none');
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }

    function userFilter(isPost = false) {
        $.ajax({
            url: APP_URL + '/user/filter',
            method: isPost ? 'POST' : 'GET',
            data: {filter: userObject.filterTree.dates},
            success: function (response) {
                if (response.userFilter) {
                    userObject.filterTree.dates = response.userFilter.date_filter;
                    if (userObject.filterTree.datesTreeObject && userObject.filterTree.dates) {
                        userObject.filterTree.datesTreeObject.values = userObject.filterTree.dates;
                        if (userObject.objDetail) {
                            userObject.objDetail.filterTree.dates = userObject.filterTree.dates;
                        }
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }

    //</editor-fold>

    //<editor-fold desc="FUNCTIONS TOOLS">

    function elementExists(object) {
        if (object !== null && object !== undefined) {
            let element = $('#' + object.element);
            if (element !== null && element !== undefined) {
                return element.length;
            } else {
                return object.length;
            }
        }
        return false;
    }

    function dynamicColors(uniqueColors) {
        let color = {
            r: Math.floor(Math.random() * 255),
            g: Math.floor(Math.random() * 255),
            b: Math.floor(Math.random() * 255)
        };
        let exists = false;
        do {
            exists = uniqueColors.some(function (uniqueColor) {
                return uniqueColor.r === color.r && uniqueColor.g === color.g && uniqueColor.b === color.b;
            });
        } while (exists);
        uniqueColors.push(color);
        return "rgb(" + color.r + "," + color.g + "," + color.b + ")";
    }

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }

    function createChild(row, object, data = null) {
        detailClick = true;
        // This is the table we'll convert into a DataTable
        let objectChild = object.objDetail;
        var tableDom = '<table id="' + objectChild.element + '" class="table-details table table-bordered table-valign-middle capitalize"/>';
        var canvasDom = '<div class="col-12"><canvas id="' + objectChild.element + '-Chart"/></div>';
        objectChild.objChart.element_id = objectChild.element + '-Chart';

        let objectChildItem = {...objectChild};
        object.children.push(objectChildItem);

        let createdChild = tableDom;
        // Display it the child row
        row.child($(tableDom)).show();
        let table = $('#' + objectChildItem.element);
        table.after(canvasDom);
        // row.child(objectChild.element).show();
        data = {...data, 'route': object.routeData};
        getColumns(objectChildItem, data, {
            removeTotal: false,
            removeTotalColumn: false,
            details: false,
            refreshMode: false,
            pagination: false
        });
        // InitDataTable(objectChild, data, {removeTotal: false, removeTotalColumn: false, details: false});
    }

    function destroyChild(row) {
        var table = $("table", row.child());
        table.detach();
        table.DataTable().destroy();

        // And then hide the row
        row.child.hide();
    }

    function toggleLoader(parent, remove = false) {
        if (remove) {
            parent.find('.loader_wrapper').remove();
            parent.find('.loader_container').remove();
        } else {
            parent.append('<div class="loader_wrapper"><div class="loader"></div></div>');
            parent.append('<div class="loader_container"></div>');
        }
    }

    //</editor-fold>

    //<editor-fold desc="GLOBAL FILTER">
    $('#filterDashboard').on('change', function () {
        let url = APP_URL + '/' + $(this).val();
        if ($('#filterDashboard').prop('selectedIndex') && url !== window.location.href) {
            window.location = url;
        }
    });

    $("#refreshAll").on('click', function () {

        toggleLoader($(this).parents('.col-12'));

        globalElements.map(function (element) {
            element.filterTree.dates = userObject.filterTree.dates;
            element.filterTree.datesTreeObject.values = userObject.filterTree.dates;
        });
        userFilter(true);
        getColumns(statsCallsCloture, filterData(), {
            removeTotal: false,
            refreshMode: true,
            removeTotalColumn: false,
            details: false,
            pagination: false
        });
        getColumns(statsFoldersByCode, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(statsFoldersByType, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(statsColturetech, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(statsGlobalDelay, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
    });
//</editor-fold>
    $("#printElement").on("click", function () {
        let statsCallsClotureChart = document.getElementById('statsCallsClotureChart');
        let statsFoldersByTypeChart = document.getElementById('statsFoldersByTypeChart');
        let statsFoldersByCodeChart = document.getElementById('statsFoldersByCodeChart');
        let statsColturetechChart = document.getElementById('statsColturetechChart');
        let statsGlobalDelayChart = document.getElementById('statsGlobalDelayChart');

        //creates image
        let statsCallsClotureChartImg = statsCallsClotureChart.toDataURL("image/png", 1.0);
        let statsFoldersByTypeChartImg = statsFoldersByTypeChart.toDataURL("image1/png", 1.0);
        let statsFoldersByCodeChartImg = statsFoldersByCodeChart.toDataURL("image2/png", 1.0);
        let statsColturetechChartImg = statsColturetechChart.toDataURL("image3/png", 1.0);
        let statsGlobalDelayChartImg = statsGlobalDelayChart.toDataURL("image4/png", 1.0);

        //creates PDF from img
        let doc = new jsPDF('p', 'pt', [ 842,  842]);
        doc.text(10, 20, 'Répartition des dossiers traités sur le périmètre validation, par catégorie de traitement');
        doc.autoTable({html: '#statsCallsCloture', margin: {top: 30}, pageBreak: 'auto' });
        doc.addImage(statsCallsClotureChartImg, 'JPEG',150 , ($('#statsCallsCloture').height()/1.328147) + 30 , 500 , 350);
        doc.addPage();
        doc.text(10, 20, 'Répartition des dossiers non validés par Code Type intervention');
        doc.autoTable({html: '#statsFoldersByType', margin: {top: 30} , pageBreak: 'auto'});
        doc.addPage();
        doc.text(10, 20, 'la charte de Répartition des dossiers non validés par Code Type intervention');
        doc.addImage(statsFoldersByTypeChartImg, 'JPEG', 150, 30, 500, 500);
        doc.addPage();
        doc.text(10, 20, 'Répartition des dossiers non validés par code intervention');
        doc.autoTable({html: '#statsFoldersByCode', margin: {top: 30} , pageBreak: 'auto'});
        doc.addPage();
        doc.text(10, 20, 'la charte de Répartition des dossiers non validés par code intervention');
        doc.addImage(statsFoldersByCodeChartImg, 'JPEG', 150, 30, 500, 500);
        doc.addPage();
        doc.text(10, 20, 'Délai de validation post solde');
        doc.autoTable({html: '#statsColturetech', margin: {top: 30} , pageBreak: 'auto'});
        doc.addImage(statsColturetechChartImg, 'JPEG',150, ($('#statsColturetech').height()/1.328147) + 30 , 500 , 350);
        doc.addPage();
        doc.text(10, 20, 'Délai global de traitement OT');
        doc.autoTable({html: '#statsGlobalDelay', margin: {top: 30} , pageBreak: 'auto'});
        doc.addImage(statsGlobalDelayChartImg, 'JPEG',150 ,($('#statsGlobalDelay').height()/1.328147) + 30 , 500 , 350);
        doc.save('Appels Clôture.pdf');
    })
});
