define(
    ['text!templates/caloriesList.html', 'tools/api_client', 'text!templates/loading.html'],
    function (template, apiClient, loadingTemplate) {
        var module = {
            page: 1,
            init: function (params) {
                $('#app').hide().html(loadingTemplate).show();

                if(!params){
                    params = {};
                }

                if(params.page){
                    module.page = parseInt(params.page);
                }

                apiClient.getNotes(null, null, null, null, module.page, module.render, module.onFail);
            },
            template: require('handlebars').compile(template),
            onFail: function (data) {
                console.log(data);
                Materialize.toast(data.error_message, 4000);
                location.hash = '#';
            },
            render: function (data) {
                console.log(data);
                var templateData = {
                    'notes': data.notes,
                    'total_calories': data.total_calories,
                    'show_next_page': data.has_more_pages,
                    'show_prev_page': module.page > 1,
                    'next_page': module.page + 1,
                    'prev_page': module.page - 1
                };
                var html = module.template(templateData);
                $('#app').hide().html(html).show();
                module.bindActions();
            },
            bindActions: function () {
                $('.login__registerLink').click(module.onRegisterClick);
                $('.login__form').submit(module.onFormSubmit);
                $('.datepicker').pickadate({
                    selectMonths: true, // Creates a dropdown to control month
                    selectYears: 15 // Creates a dropdown of 15 years to control year
                });
                $('select').material_select();
            }
        };

        return {
            init: module.init
        }
    }
);