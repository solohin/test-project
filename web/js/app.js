define(
    ['router', 'tools/api_client', 'handlebars', 'text!templates/menu.html'],
    function (router, apiClient, handlebars, menuTemplate) {
        var module = {
            role: '',
            getRole: function () {
                return module.role;
            },
            init: function () {
                router.init();
            },
            allUsers: null,
            getUsers: function (callback) {
                if (module.allUsers === null) {
                    var success = function (data) {
                        module.allUsers = data;
                        callback(data);
                    };
                    var fail = function (data) {
                        console.log(data);
                        Materialize.toast(data.error_message, 4000);
                        location.hash = '#';
                    };
                    apiClient.getUsers(success, fail);
                } else {
                    callback(module.allUsers);
                }

            },
            menuGenerator: require('handlebars').compile(menuTemplate),
            getMenu: function () {
                var role = module.getRole();
                return module.menuGenerator({
                    role_admin: role == 'ROLE_ADMIN',
                    role_user: role == 'ROLE_USER',
                    role_manager: role == 'ROLE_MANAGER'
                });
            },
            handleAuthSuccess: function (role) {
                module.role = role;
                if (role == 'ROLE_ADMIN') {
                    location.hash = '#calories_list';
                    Materialize.toast('You have admin permissions', 4000)
                } else if (role == 'ROLE_MANAGER') {
                    location.hash = '#users_list';
                    Materialize.toast('You have manager permissions', 4000)
                } else {
                    location.hash = '#calories_list';
                }
            }
        };

        return {
            init: module.init,
            getRole: module.getRole,
            getUsers: module.getUsers,
            getMenu: module.getMenu,
            handleAuthSuccess: module.handleAuthSuccess
        }
    }
);