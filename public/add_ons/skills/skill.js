$(function () {
    var _skills = $('#skills');
    var _topSkills = $('#topSkills');

    InitLoad();

    function InitLoad() {
        _skills.find('option').remove().end();
        _topSkills.find('option').remove().end();
        $.ajax({
            type: 'GET',
            url: '/getSkills'
        }).then(function (data) {
            // create the option and append to Select2
            data.skills.forEach((skill, index) => {
                var option = new Option(skill.name, skill.id, false, skill.assigned);
                _skills.append(option).trigger('change');
            });
            data.skills.forEach((skill, index) => {
                var option = new Option(skill.name, skill.id, false, skill.isTopSkill === 1);
                _topSkills.append(option).trigger('change');
            });

            // manually trigger the `select2:select` event
            // _skills.trigger({
            //     type: 'select2:select',
            //     params: {
            //         data: data.skills
            //     }
            // });
        });
    }

    $('.update-skills').on('click', function (e) {
        e.preventDefault();
        var _skillsOpts = $('#skills option:selected');
        var _topSkillsOpts = $('#topSkills option:selected');
        skills = [];
        Object.values(_skillsOpts).forEach(function (skill, index) {
            if (index < _skillsOpts.length) {
                skills.push($(skill).val())
            }
        });
        topSkills = [];
        Object.values(_topSkillsOpts).forEach(function (skill, index) {
            if (index < _topSkillsOpts.length) {
                topSkills.push($(skill).val())
            }
        });
        var baseUrl = APP_URL;
        $.ajax({
            method: 'PUT',
            url: baseUrl + '/updateSkills',
            data: {skills, topSkills},
            success: function (response) {
                feedBack(response.message, 'success');

                // _skills.select2('data', {id: null, text: null});
                _skills.select2("destroy");
                // _topSkills.select2('data', {id: null, text: null});
                _topSkills.select2("destroy");
                InitLoad();
                // _skills.val(null).trigger('change');
                _skills.select2(InitSelect2());

                // _topSkills.val(null).trigger('change');
                _topSkills.select2(InitSelect2(4));
                // _skills.select2();
                // _skills.select2("refresh");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 401 || jqXHR.status === 422) {
                    feedBack(jqXHR.responseJSON.message, 'error');
                }
                // $(_this).prop('checked', !$(element).prop('checked'));
            }
        });
    });

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }

    function InitSelect2(maxSelectionLength = null) {
        return {
            multiple: true,
            maximumSelectionLength: maxSelectionLength,
            allowClear: true,
            width: 'resolve',
            // theme: 'classic',
            placeholder: 'Select a Skill',
            // placeholder: {
            //     id: '0', // the value of the option
            //     text: 'Select an option'
            // },
            ajax: {
                url: '/getSkills',
                type: 'GET',
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    params.term = params.term === '*' ? '' : params.term;
                    return {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        searchTerm: $.trim(params.term)
                    };
                },
                processResults: function (response) {
                    results = response.skills.map(function (skill) {
                        skill.text = skill.name;
                        return skill;
                    });
                    return {
                        results: results
                    };
                },
                cache: true
            },
            templateResult: function (data, x) {
                if (data.loading) {
                    return data.text;
                }
                return data.name;
            }
        };
    }

    _skills.select2(InitSelect2());
    _topSkills.select2(InitSelect2(4));
});
