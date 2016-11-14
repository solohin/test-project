define(
    ['text!templates/notesList.html', 'tools/api_client', 'text!templates/loading.html', 'handlebars'],
    function (template, apiClient, loadingTemplate) {
        var module = {
            page: 1,
            date_from: null,
            date_to: null,
            time_from: null,
            time_to: null,
            init: function (params) {
                $('#app').hide().html(loadingTemplate).show('fast');

                if (!params) {
                    params = {};
                }

                //default values
                module.page = 1;
                module.date_to = null;
                module.date_from = null;
                module.time_from = null;
                module.time_to = null;

                //values from request
                if (params.page) {
                    module.page = parseInt(params.page);
                }
                if (params.date_to && params.date_to.split('.').length === 3) {
                    module.date_to = params.date_to;
                }
                if (params.date_from && params.date_from.split('.').length === 3) {
                    module.date_from = params.date_from;
                }
                if (params.time_from && params.time_from.split(':').length === 2) {
                    module.time_from = params.time_from;
                }
                if (params.time_to && params.time_to.split(':').length === 2) {
                    module.time_to = params.time_to;
                }

                console.log('params from URL', params);

                apiClient.getNotes(module.date_from, module.date_to, module.time_from, module.time_to, module.page, module.render, require('app').onError);
            },
            template: require('handlebars').compile(template),
            onFail: function (data) {
                console.log(data);
                Materialize.toast(data.error_message, 4000);
                location.hash = '#';
            },
            render: function (data) {
                console.log(data);

                var notes = $.map(data.notes, function (val) {
                    val['class'] = val.daily_normal ? '_green' : '_red';
                    return val;
                });

                var templateData = {
                    'notes': notes,
                    'total_calories': data.total_calories,
                    'show_next_page': data.has_more_pages,
                    'show_prev_page': module.page > 1,
                    'next_page': module.page + 1,
                    'prev_page': module.page - 1,
                    'menu_html': require('app').getMenu()
                };
                var html = module.template(templateData);
                $('#app').hide().html(html).show('fast');
                module.bindActions();
                module.roleFilters(require('app').getRole());
            },
            roleFilters: function (role) {
                if (role == 'ROLE_ADMIN') {
                    //nothing
                } else if (role == 'ROLE_MANAGER') {
                    location.hash = '#';//Can not access
                    var err = 'ROLE_MANAGER should not be here!';
                    console.log(err);
                    Materialize.toast(err, 4000);
                } else {
                    $('._roleUserHide').hide();
                }
            },
            applyFilters: function () {
                var fromDate = module.dateFromPicker.val();
                var toDate = module.dateToPicker.val();
                var fromTime = module.timeFromPicker.val();
                var toTime = module.timeToPicker.val();

                var data = {};
                if (fromDate.split('.').length === 3) {
                    data['date_from'] = fromDate;
                }
                if (toDate.split('.').length === 3) {
                    data['date_to'] = toDate;
                }
                if (fromTime.split(':').length === 2) {
                    data['time_from'] = fromTime;
                }
                if (toTime.split(':').length === 2) {
                    data['time_to'] = toTime;
                }
                var paramUrl = $.param(data);

                var newHash = '#notes_list';
                if (paramUrl) {
                    newHash += '?' + paramUrl;
                }
                location.hash = newHash;
            },
            dateFromPicker: null,
            dateToPicker: $('.notesList__dateTo'),
            timeFromPicker: $('.notesList__timeFrom'),
            timeToPicker: $('.notesList__timeTo'),
            bindActions: function () {
                module.dateFromPicker = $('.notesList__dateFrom');
                module.dateToPicker = $('.notesList__dateTo');
                module.timeFromPicker = $('.notesList__timeFrom');
                module.timeToPicker = $('.notesList__timeTo');

                $('.login__registerLink').click(module.onRegisterClick);
                $('.login__form').submit(module.onFormSubmit);

                var params = {
                    selectMonths: true,
                    selectYears: 15,
                    format: 'dd.mm.yyyy'
                };

                module.dateFromPicker.pickadate(params).val(module.date_from);
                module.dateToPicker.pickadate(params).val(module.date_to);

                module.timeFromPicker.val(module.time_from);
                module.timeToPicker.val(module.time_to);

                $('select').material_select();
                $('.notesList__filterButt').click(module.applyFilters);
            }
        };

        return {
            init: module.init
        }
    }
);