define(
    [
        'modules/login',
        'modules/register',
        'modules/caloriesList'
    ], function (
        loginModule,
        registerModule,
        caloriesListModule
    ) {
    var module = {
        role: '',
        routers: [
            {'pattern': /register/, action: registerModule.init},
            {'pattern': /login/, action: loginModule.init},
            {'pattern': /calories_list/, action: caloriesListModule.init},
            {'pattern': /users_list/, action: function(){}}
        ],
        defaultAction: function(){
            location.hash = '#login';
        },
        init: function () {
            window.addEventListener("hashchange", module.onHashChange, false);
            module.onHashChange();


        },
        onHashChange: function () {
            for (var i = 0; i < module.routers.length; i++) {
                var regexp = module.routers[i].pattern;
                if (location.hash.substr(1).match(regexp)) {
                    console.log('Mathed route ' + regexp);
                    console.log('Function is ' + module.routers[i].action);
                    module.routers[i].action();
                    return;
                }
            }
            module.defaultAction();
        }
    };

    return {
        init: module.init
    }
});