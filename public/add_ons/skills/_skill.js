$(function () {
    var _skills = $('#skills');
    $.ajax({
        type: 'GET',
        url: '/getUserSkills'
    }).then(function (data) {
        // create the option and append to Select2
        data.skills.forEach((skill, index) => {
            var option = new Option(skill.name, skill.id, false, skill.isTopSkill === 1);
            _skills.append(option).trigger('change');
        });

        // manually trigger the `select2:select` event
        // _skills.trigger({
        //     type: 'select2:select',
        //     params: {
        //         data: data.skills
        //     }
        // });
    });

    _skills.select2({
        multiple: true,
        maximumSelectionLength: 4,
        allowClear: true,
        width: 'resolve',
        // theme: 'classic',
        placeholder: 'Select a Skill',
        // placeholder: {
        //     id: '0', // the value of the option
        //     text: 'Select an option'
        // },
        ajax: {
            url: '/getUserSkills',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                console.log(params);
                params.term = params.term === '*' ? '' : params.term;
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    search: $.trim(params.term)
                };
            },
            processResults: function (response) {
                console.log(response.skills);
                results = response.skills.map(function (skill) {
                    skill.text = skill.name;
                    return skill;
                });
                return {
                    results: results
                };
            },
            cache: true,
            success: function(response) {
                console.log('hi');
                console.log(response);
            }
        },
        templateResult: function (data, x) {
            if (data.loading) {
                return data.text;
            }
            return data.name;
        }
    });
});
