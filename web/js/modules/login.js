define(
    ['text!templates/login.html', 'modules/register', 'tools/api_client', 'handlebars'],
    function (template, registerModule, apiClient) {
        var module = {
            init: function () {
                $('#app').hide().html(template).show('fast');
                module.bindActions();

                //TODO debug
                //$('.login__username').val('dummyManager');
                $('.login__username').val('dummyAdmin');
                //$('.login__username').val('dummyUser');
                $('.login__password').val('Dummy Password 12345');
            },
            bindActions: function () {
                $('.login__form').submit(module.onFormSubmit);
            },
            onFormSubmit: function (e) {
                e.preventDefault();
                var username = $('.login__username').val();
                var password = $('.login__password').val();
                apiClient.login(username, password, module.onLogin.success, module.onLogin.fail);
            },
            onLogin: {
                success: function (data) {
                    Materialize.toast('Successful log in', 4000)
                    require('app').handleAuthSuccess(data.role);
                },
                fail: function (data) {
                    console.log(data);
                    Materialize.toast(data.error_message, 4000)
                }
            }
        };

        return {
            init: module.init
        }
    }
);