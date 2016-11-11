define(['router', 'handlebars'], function (router) {
    var module = {
        role: '',
        getRole: function () {
            return module.role;
        },
        init: function () {
            router.init();
        },
        handleAuthSuccess: function (role) {
            module.role = role;
            if (role == 'ROLE_ADMIN') {
                location.hash = '#calories_list';
                Materialize.toast('You have admin permissions', 4000)
            } else if (role == 'ROLE_MANAGER') {
                location.hash = '#users_list';
                Materialize.toast('You have manager permissions', 4000)
                Materialize.toast('TODO', 4000)
            } else {
                location.hash = '#calories_list';
            }
        }
    };

    return {
        init: module.init,
        getRole: module.getRole,
        handleAuthSuccess: module.handleAuthSuccess
    }
});