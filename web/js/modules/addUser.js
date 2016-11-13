define(
    ['text!templates/addUser.html', 'tools/api_client', 'handlebars'],
    function (template, apiClient) {
        var module = {
            init: function (params) {
                var templateData = {
                    'menu_html': require('app').getMenu()
                };
                $('#app').hide().html(module.template(templateData)).show('fast');
                module.bindActions();
            },
            template: require('handlebars').compile(template),
            onFormSubmit: function (e) {
                e.preventDefault();
                var username = $('.addUser__username').val();
                var password = $('.addUser__password').val();
                apiClient.addUser(username, password, module.onSuccess, module.onFail);
            },
            onSuccess: function (data) {
                Materialize.toast('User added!', 4000);
                location.hash = '#users_list';
            },
            onFail: function (data) {
                Materialize.toast(data.error_message, 4000);
            },
            bindActions: function () {
                Materialize.updateTextFields();
                $('.addUser__form').on('submit', module.onFormSubmit)
            }
        };

        return {
            init: module.init
        }
    }
);