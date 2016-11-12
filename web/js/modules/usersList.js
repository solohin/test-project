define(
    ['text!templates/usersList.html', 'tools/api_client', 'text!templates/loading.html', 'handlebars'],
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

                console.log('params from URL', params);

                apiClient.getUsers(module.onGetSuccess, module.onFail);
            },
            template: require('handlebars').compile(template),
            onFail: function (data) {
                console.log(data);
                Materialize.toast(data.error_message, 4000);
                location.hash = '#';
            },
            onGetSuccess: function (data) {
                console.log(data);

                var templateData = {
                    'users': data.users,
                    'menu_html': require('app').getMenu()
                };
                var html = module.template(templateData);
                $('#app').hide().html(html).show('fast');
                module.roleFilters(require('app').getRole());
            },
            roleFilters: function (role) {
                if (role == 'ROLE_ADMIN') {
                    //nothing
                } else if (role == 'ROLE_USER') {
                    location.hash = '#';//Can not access
                    var err = 'ROLE_USER should not be here!';
                    console.log(err);
                    Materialize.toast(err, 4000);
                } else {
                    $('._roleUserHide').hide();
                }
            }
        };

        return {
            init: module.init
        }
    }
);