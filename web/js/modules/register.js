define(
    ['text!templates/register.html', 'tools/api_client'],
    function (template, apiClient) {
        var module = {
            init: function () {
                $('#app').hide().html(template).show('fast');
                module.bindActions();
            },
            bindActions: function () {
                $('.register__form').submit(module.onRegisterFormSubmit);
            },
            onRegisterFormSubmit: function (e) {
                e.preventDefault();
                var username = $('.register__username').val();
                var password = $('.register__password').val();
                apiClient.register(username, password, module.onRegister.success, module.onRegister.fail);
            },
            onRegister: {
                success: function (data) {
                    Materialize.toast('Successfully registered', 4000)
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