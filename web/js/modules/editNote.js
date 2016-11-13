define(
    ['text!templates/editNote.html', 'tools/api_client', 'text!templates/loading.html', 'handlebars'],
    function (template, apiClient, loadingTemplate) {
        var module = {
            noteId: null,
            init: function (params) {
                $('#app').hide().html(loadingTemplate).show('fast');
                console.log('params from URL', params);
                module.noteId = params.id;
                apiClient.getNote(params.id, module.onGetSuccess, require('app').onError);
            },
            template: require('handlebars').compile(template),
            users: null,
            onPostSuccess: function (data) {
                Materialize.toast('Note updated!', 4000);
            },
            onGetSuccess: function (data) {
                var render = function () {
                    var templateData = data.note;
                    templateData.menu_html = require('app').getMenu();
                    templateData.is_admin = isAdmin;
                    templateData.users = $.map(module.users, function (val) {
                        val.selected = (val.id == data.note.user_id);
                        return val;
                    });


                    var html = module.template(templateData);
                    $('#app').hide().html(html).show('fast');
                    module.bindActions();
                };

                var isAdmin = (require('app').getRole() == 'ROLE_ADMIN');
                if (isAdmin) { // get isersList
                    require('app').getUsers(function (users) {
                        module.users = users;
                        render();
                    });
                } else {
                    render();
                }
            },
            onFormSubmit: function (e) {
                e.preventDefault();
                apiClient.updateNote(
                    module.noteId,
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