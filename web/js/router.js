define(
    [
        'modules/login',
        'modules/register',
        'modules/notesList',
        'modules/editNote',
        'modules/editUser',
        'modules/usersList',
        'modules/addUser',
        'modules/addNote',
    ], function (loginModule,
                 registerModule,
                 notesListModule,
                 editNoteModule,
                 editUserModule,
                 usersListModule,
                 addUserModule,
                 addNoteModule
    ) {
        var module = {
            role: '',
            routers: [
                {'pattern': /^register/, action: registerModule.init},
                {'pattern': /^login/, action: loginModule.init},
                {'pattern': /^notes_list.*/, action: notesListModule.init},
                {'pattern': /^edit_note.*/, action: editNoteModule.init},
                {'pattern': /^edit_user.*/, action: editUserModule.init},
                {'pattern': /^users_list.*/, action: usersListModule.init},
                {'pattern': /^add_user/, action: addUserModule.init},
                {'pattern': /^add_note/, action: addNoteModule.init},
                {
                    'pattern': /^settings/,
                    action: function () {
                        editUserModule.init({'id': 'me'});
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