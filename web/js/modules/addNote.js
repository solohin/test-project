define(
    ['text!templates/editNote.html', 'tools/api_client', 'handlebars'],
    function (template, apiClient) {
        var module = {
            users: null,
            isAdmin: null,
            init: function (params) {
                module.isAdmin = (require('app').getRole() == 'ROLE_ADMIN');
                if (module.isAdmin && module.users === null) {
                    require('app').getUsers(function (users) {
                        module.users = users;
                        module.render();
                    });
                } else {
                    module.render();
                }
            },
            render: function () {
                var templateData = {};
                templateData.menu_html = require('app').getMenu();
                templateData.is_admin = module.isAdmin;
                templateData.title = 'Add note';
                templateData.users = module.users;

                var html = module.template(templateData);
                $('#app').hide().html(html).show('fast');
                module.bindActions();
            },
            template: require('handlebars').compile(template),
            onPostSuccess: function (data) {
                Materialize.toast('Note added!', 4000);
                location.hash = '#edit_note?id=' + data.id;
            },
            onFormSubmit: function (e) {
                e.preventDefault();
                apiClient.addNote(
                    $('#editNote__text').val(),
                    $('#editNote__date').val(),
                    $('#editNote__time').val(),
                    $('#editNote__calories').val(),
                    $('#editNote__userId').val(),
                    module.onPostSuccess,
                    require('app').onError
                );
            },
            bindActions: function () {
                Materialize.updateTextFields();

                var params = {
                    selectMonths: true,
                    selectYears: 1,
                    format: 'dd.mm.yyyy',
                    clear: false
                };

                $('#editNote__date').pickadate(params);
                $('select').material_select();
                $('.editNote__form').on('submit', module.onFormSubmit);
            }
        };

        return {
            init: module.init
        }
    }
);