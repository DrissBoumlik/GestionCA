$(function () {
    $('#page-container').addClass('sidebar-mini');

    let agent_name = '';
    let agence_code = '';
    ajaxRequests = 0;


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
        routeCol: 'regions/details/groupement/columns?key_groupement=Appels clôture',
        routeData: 'regions/details/groupement?key_groupement=Appels clôture',
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
            queryJoin: ' and Groupement like "Appels clôture" and Resultat_Appel like "Appels clôture - CRI non conforme" ',
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
        getColumns(statsFoldersByType, filterData(), {
            removeTotal: false,
            refreshMode: false,
            details: false,
            removeTotalColumn: false,
            pagination: false,
            searching: false
        });
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
            queryJoin: ' and Groupement like "Appels clôture" and Resultat_Appel like "Appels clôture - CRI non conforme" ',
            subGroupBy: ' GROUP BY Id_Externe, Nom_Region, Code_Intervention , Resultat_Appel) groupedst ',
            queryGroupBy: ' GROUP BY st.Id_Externe,Nom_Region, Code_Intervention , Resultat_Appel'
        },
        routeCol: 'nonValidatedFolders/columns/Code_Intervention?key_groupement=Appels clôture',
        routeData: 'nonValidatedFolders/Code_Intervention?key_groupement=Appels clôture',
        objChart: {
            element_chart: undefined,
            element_id: 'statsFoldersByCodeChart',
            data: undefined,
            chartTitle: 'Répartition des dossiers non validés par code intervention'
        }
    };
    if (elementExists(statsFoldersByCode)) {
        getColumns(statsFoldersByCode, filterData(), {
            removeTotal: false,
            refreshMode: false,
            details: false,
            removeTotalColumn: false,
            pagination: false,
            searching: false
        });
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
        routeCol: 'Cloturetech/columns?key_groupement=Appels clôture',
        routeData: 'Cloturetech?key_groupement=Appels clôture',
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
            appCltquery: true,
        },
        routeCol: 'GlobalDelay/columns?key_groupement=Appels clôture',
        routeData: 'GlobalDelay?key_groupement=Appels clôture',
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

    detailClick = false;

    getDatesFilter(globalElements);

    userFilter(userObject);

    //<editor-fold desc="GLOBAL FILTER">
    $('#filterDashboard').on('change', function () {
        filterSelectOnChange(this, agence_code, agent_name);
    });

    $("#refreshAll").on('click', function () {

        toggleLoader($(this).parents('.col-12'));

        globalElements.map(function (element) {
            element.filterTree.dates = userObject.filterTree.dates;
            element.filterTree.datesTreeObject.values = userObject.filterTree.dates;
        });
        userFilter(userObject, true);
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
        toggleLoader($('body'));

        setTimeout(function () {
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
            doc.addImage(statsFoldersByTypeChartImg, 'JPEG', 532 , 30 , 350 , 300);
            doc.autoTable({html: '#statsFoldersByType', margin: {left: 0 , top: 30}, pageBreak: 'auto',styles: {cellPadding: {top: 0, bottom: 0,right : 0}}, tableWidth: 525, columnStyles: { 6: {cellWidth: 45 }, 5:{cellWidth: 45 } } });
            doc.addPage();
            doc.text(10, 20, 'Répartition des dossiers non validés par code intervention');
            doc.addImage(statsFoldersByCodeChartImg, 'JPEG', 532 , 30 , 350 , 300);
            doc.autoTable({html: '#statsFoldersByCode', margin: {left: 0 , top: 30}, pageBreak: 'auto',styles: {cellPadding: {top: 0, bottom: 0,right : 0}} , tableWidth: 525});
            doc.addPage();
            doc.text(10, 20 , 'Délai de validation post solde');
            doc.autoTable({html: '#statsColturetech', margin: {left: 0 , top: 30}, pageBreak: 'auto', tableWidth: 520 });
            doc.addImage(statsColturetechChartImg, 'JPEG',532, 30 , 350 , 300);
            doc.text(10, 390 , 'Délai global de traitement OT');
            doc.autoTable({html: '#statsGlobalDelay',pageBreak: 'auto', tableWidth: 520, startY: 400, margin: {left: 0} });
            doc.addImage(statsGlobalDelayChartImg, 'JPEG',532 , 400 , 350 , 300);
            doc.save('Appels Clôture.pdf');

            toggleLoader($('body'), true);
        }, 100);
    })
});
