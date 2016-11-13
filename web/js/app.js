define(
    ['router', 'tools/api_client', 'handlebars', 'text!templates/menu.html'],
    function (router, apiClient, handlebars, menuTemplate) {
        var module = {
            role: '',
            getRole: function () {
                return module.role;
            },
            init: function () {
                module.restoreToken();

                var successLogin = function (data) {
                    module.handleAuthSuccess(data.user.role, true);
                    router.init();
                };
                var loginFailed = router.init;

                apiClient.getUser('me', successLogin, loginFailed);
            },

            localStorageApiKey: 'calories_tracker_api_token',
            restoreToken: function () {
                var token = localStorage.getItem(module.localStorageApiKey);
                apiClient.setToken(token);
            },
            saveToken: function (token) {
                localStorage.setItem(module.localStorageApiKey, token);
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


            onError: function (data) {
                console.log(data);
                Materialize.toast(data.error_message, 4000);
                if (data.error_type == 'no_token' || data.error_type == 'wrong_token') {
                    location.hash = '#login';
                }
            },
            menuGenerator: require('handlebars').compile(menuTemplate),
            getMenu: function () {
                var role = module.getRole();
                var data = {
                    role_admin: (role == 'ROLE_ADMIN'),
                    role_user: (role == 'ROLE_USER'),
                    role_manager: (role == 'ROLE_MANAGER'),
                    role: role
                };
                console.log('menu data', data);
                return module.menuGenerator(data);
            },
            handleAuthSuccess: function (role, stayOnThisPage) {
                module.role = role;

                if (module.role == 'ROLE_ADMIN') {
                    if(!stayOnThisPage){
                        location.hash = '#users_list';
                    }
                    Materialize.toast('You have admin permissions', 4000)
                } else if (module.role == 'ROLE_MANAGER') {
                    if(!stayOnThisPage){
                        location.hash = '#users_list';
                    }
                    Materialize.toast('You have manager permissions', 4000)
                } else {
                    if(!stayOnThisPage){
                        location.hash = '#calories_list';
                    }
                }
            }
        };

        return {
            init: module.init,
            getRole: module.getRole,
            getUsers: module.getUsers,
            saveToken: module.saveToken,
            getMenu: module.getMenu,
            onError: module.onError,
            handleAuthSuccess: module.handleAuthSuccess
        }
    }
);