define(
    ['text!templates/caloriesList.html', 'tools/api_client', 'text!templates/loading.html'],
    function (template, apiClient, loadingTemplate) {
        var module = {
            init: function () {
                $('#app').hide().html(loadingTemplate).show();
                apiClient.getNotes(null, null, null, null, module.render, module.onFail);
            },
            onFail: function (data) {
                console.log(data);
                Materialize.toast(data.error_message, 4000)
                location.hash = '#';
            },
            render: function () {
                $('#app').hide().html(template).show();
                module.bindActions();
            },
            bindActions: function () {
                $('.login__registerLink').click(module.onRegisterClick);
                $('.login__form').submit(module.onFormSubmit);
            }
        };

        return {
            init: module.init
        }
    }
);