define(
    [
        'modules/login',
        'modules/register',
        'modules/caloriesList',
        'modules/editNote'
    ], function (loginModule,
                 registerModule,
                 caloriesListModule,
                 editNoteModule
    ) {
        var module = {
            role: '',
            routers: [
                {'pattern': /^register/, action: registerModule.init},
                {'pattern': /^login/, action: loginModule.init},
                {'pattern': /^calories_list.*/, action: caloriesListModule.init},
                {'pattern': /^editNote.*/, action: editNoteModule.init},
                {
                    'pattern': /^users_list.*/, action: function () {
                }
                }
            ],
            defaultAction: function () {
                location.hash = '#login';
            },
            init: function () {
                window.addEventListener("hashchange", module.onHashChange, false);
                module.onHashChange();
            },
            getHashData: function () {
                var data = {};
                if (location.hash.indexOf('?') !== -1) {
                    var query = location.hash.split('?')[1];
                    var couples = query.split('&');
                    for (var k = 0; k < couples.length; k++) {
                        var couple = couples[k].split('=');
                        if (couple.length == 2) {
                            data[couple[0]] = decodeURIComponent(couple[1]);
                        }
                    }
                }
                return data;
            },
            onHashChange: function () {
                for (var i = 0; i < module.routers.length; i++) {
                    var regexp = module.routers[i].pattern;
                    var match = location.hash.substr(1).match(regexp);
                    if (match) {
                        console.log('Mathed route ' + regexp);
                        console.log('Function is ' + module.routers[i].action);
                        console.log('Data is ', module.getHashData());
                        module.routers[i].action(module.getHashData());
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