define(
    ['text!templates/editUser.html', 'tools/api_client', 'text!templates/loading.html', 'handlebars'],
    function (template, apiClient, loadingTemplate) {
        var module = {
            userId: null,
            init: function (params) {
                $('#app').hide().html(loadingTemplate).show('fast');
                console.log('params from URL', params);
                module.userId = params.id;
                apiClient.getUser(params.id, module.onGetSuccess, module.onFail);
            },
            template: require('handlebars').compile(template),
            onFail: function (data) {
                console.log(data);
                Materialize.toast(data.error_message, 4000);
                location.hash = '#';
            },
            users: null,
            onPostSuccess: function (data) {
                Materialize.toast('User updated!', 4000);
            },
            onGetSuccess: function (data) {
                var app = require('app');

                var templateData = data.user;
                templateData.menu_html = app.getMenu();
                templateData.is_user = (app.getRole() == 'ROLE_USER');

                var html = module.template(templateData);
                $('#app').hide().html(html).show('fast');
                module.bindActions();
            },
            onFormSubmit: function (e) {
                e.preventDefault();
                apiClient.updateUser(
                    module.userId,
                    null,
                    null,
                    $('#editUser__dailyNormal').val(),
                    module.onPostSuccess,
                    module.onFail
                );
            },
            bindActions: function () {
                Materialize.updateTextFields();

                var params = {
                    selectMonths: true,
                    selectYears: 1,
                    format: 'dd.mm.yyyy',
                    clear: false
                };

                $('#editUser__date').pickadate(params);
                $('select').material_select();
                $('.editUser__form').on('submit', module.onFormSubmit);
            }
        };

        return {
            init: module.init
        }
    }
);