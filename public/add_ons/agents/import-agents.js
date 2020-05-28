$(document).ready(function () {
    function getWeeksStartAndEndInMonth(month, year, _start, number) {
        let weeks = [],
            firstDate = new Date(year, month, 1),
            lastDate = new Date(year, month + 1, 0),
            numDays = lastDate.getDate();
        let start = 1;
        let end = 7 - firstDate.getDay();
        if (_start === 'monday') {
            if (firstDate.getDay() === 0) {
                end = 1;
            } else {
                end = 7 - firstDate.getDay() + 1;
            }
        }
        while (start <= numDays) {
            let businessWeekEnd = end;// -2;
            if (businessWeekEnd > 0) {
                if (businessWeekEnd > start) {
                    weeks.push({start: start, end: businessWeekEnd});
                } else {
                    //Check for last day else end date is within 5 days of the week.
                    weeks.push({start: start, end: end});
                }
            }
            start = end + 1;
            end = end + 7;
            end = start === 1 && end === 8 ? 1 : end;
            if (end > numDays) {
                end = numDays;
            }
        }

        // weeks.forEach(week => {
        //     let _s = parseInt(week.start, 10)+1,
        //         _e = parseInt(week.end,10)+1;
        //     console.log(new Date(year, month, _s).toJSON().slice(0,10).split('-').reverse().join('/') + " - " + new Date(year, month, _e).toJSON().slice(0,10).split('-').reverse().join('/'));
        //     console.log(((_e-_s)+1)*8)
        // });
        return weeks.map(function (week, index) {
            number = index === 0 ? number : number + 1;
            let _s = parseInt(week.start, 10) + 1,
                _e = parseInt(week.end, 10) + 1;
            let days = [];
            for (let i = _s; i <= _e; i++) {
                days.push({
                    id: new Date(year, month, i).toJSON().slice(0, 10),
                    text: new Date(year, month, i).toJSON().slice(0, 10)
                });
            }
            return {
                id: year + '-' + (month + 1) + '-' + 'S' + number,
                text: 'S' + number,
                days: days
            };
        });
    }

    let _months = [
        {
            id: 1,
            text: 'Janvier',
        },
        {
            id: 2,
            text: 'Février',
        },
        {
            id: 3,
            text: 'Mars',
        },
        {
            id: 4,
            text: 'Avril',
        },
        {
            id: 5,
            text: 'Mai',
        },
        {
            id: 6,
            text: 'juin',
        },
        {
            id: 7,
            text: 'Juillet',
        },
        {
            id: 8,
            text: 'Août',
        },
        {
            id: 9,
            text: 'Septembre',
        },
        {
            id: 10,
            text: 'Octobre',
        },
        {
            id: 11,
            text: 'Novembre',
        },
        {
            id: 12,
            text: 'Décembre',
        },
    ];
    let currentYear = new Date().getFullYear();
    let _years = [
        {id: currentYear - 1, text: currentYear - 1},
        {id: currentYear, text: currentYear},
    ];
    // let dayOfWeek = new Date(lastYear, 0, 1).getDay();
    // let indexOfWeek = 1;
    // let _weeks = [{id: 'S'+indexOfWeek, text: 'S'+indexOfWeek, children: []}];
    // let numberOfDays = 31;
    // dayOfWeek = dayOfWeek === 0 ? 7: dayOfWeek;
    // for (let i=dayOfWeek; i>1; i--) {
    //     _weeks[0].children.unshift({
    //         id: (lastYear - 1) + '-' + 12 + '-' + numberOfDays,
    //         text: (lastYear - 1) + '-' + 12 + '-' + numberOfDays
    //     });
    //     numberOfDays--;
    // }
    let _tree = _years.map(function (year, index_1) {
        let weeksNumber = 1;
        let months = _months.map(function (month, index_2) {
            let weeks = getWeeksStartAndEndInMonth(month.id - 1, year.id, 'monday', weeksNumber);
            weeksNumber += weeks.length;
            weeksNumber = (weeks[weeks.length - 1].days.length === 7) ? weeksNumber : weeksNumber - 1;
            console.log({...month, id: year.id + '-' + month.id, children: weeks});
            return {...month, id: year.id + '-' + month.id, children: weeks};
        });
        return {...year, children: months};
    });

    // let monthElt = $('#months');
    // months.forEach((item, index) => {
    //     console.log(item);
    //     let element = ' <div class="custom-control custom-switch mb-1" style="display: inline-block;">' +
    //         '<input type="checkbox" class="custom-control-input d-none" id="month-' + index + '" name="months[]">' +
    //         '<label class="custom-control-label" for="month-' + index + '">' + item + '</label>' +
    //         '</div>'
    //     monthElt.append(element);
    // });

    let dates = null;
    new Tree('#tree-view-months', {
        data: [{id: '-1', text: 'Choisisser un/des Mois', children: _tree}],
        closeDepth: 2,
        loaded: function () {
            // this.values = ['0-0-0', '0-1-1', '0-0-2'];
            // console.log(this.selectedNodes);
            // console.log(this.values);
            // this.disables = ['0-0-0', '0-0-1', '0-0-2']
        },
        onChange: function () {
            dates = this.values;
        }
    });
    $('.treejs-switcher').click();

    $(document).on('click', '#showModalImport', function (event) {
        if (dates === null || dates === undefined || !dates.length) {
            // alert('Vous dever choisir au moin une date');
            $('#modal-import').modal('hide');
            $('#modal-block-popin').modal('show');
            return false;
        }
        return true;
    });

    $("#form-import").validate({
        errorClass: "error",
        rules: {
            file: {
                required: true,
                extension: "xlsx|xls|xlsm"
            }
        },
        messages: {
            file: {
                required: "Le fichier est obligatoire",
                extension: "Le fichier est invalide"
            }
        },
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });

    $(document).on('click', '#modal-loader', function (event) {
        $('#modal-loader').modal('show');
    });

    $(document).on('click', '#btn-import', function (event) {
        PrepareImportDataRequest();
    });


    //<editor-fold desc="FUNCTIONS IMPORT">

    function PrepareImportDataRequest() {
        if (!$('#form-import').valid()) {
            return;
        }
        $('#modal-import').modal('hide');
        let formData = new FormData($('#form-import')[0]);
        if (dates !== null && dates !== undefined) {
            formData.append('dates', dates);
        }
        event.preventDefault();

        // let importedDataElement = $('.imported-data');
        // if (importedDataElement.length) {
        //     importedDataElement.html('Veuiller patientez, <span style="color:red">Ne rafraîchissez pas la page</span>');
        // }
        $.ajax({
            method: 'post',
            url: APP_URL + '/import/agents',
            data: formData,
            dateType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                // request_resolved = true;
                // window.localStorage.setItem('request_resolved', request_resolved);
                Swal.fire({
                    // position: 'top-end',
                    type: 'success',
                    title: data.message,
                    showConfirmButton: true,
                    customClass: {
                        confirmButton: 'btn btn-success m-1',
                    },
                    confirmButtonText: 'Ok',
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // request_resolved = true;
                // window.localStorage.setItem('request_resolved', request_resolved);
                Swal.fire({
                    // position: 'top-end',
                    type: 'error',
                    title: 'Une erreur est survenue',
                    showConfirmButton: true,
                    customClass: {
                        confirmButton: 'btn btn-success m-1',
                    },
                    confirmButtonText: 'Ok',
                });
            }
        });
    }

    //</editor-fold>
});
