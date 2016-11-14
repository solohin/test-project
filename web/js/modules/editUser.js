define(
    ['text!templates/editUser.html', 'tools/api_client', 'text!templates/loading.html', 'handlebars'],
    function (template, apiClient, loadingTemplate) {
        var module = {
            userId: null,
            init: function (params) {
                $('#app').hide().html(loadingTemplate).show('fast');
                console.log('params from URL', params);
                module.userId = params.id;
                apiClient.getUser(params.id, module.onGetSuccess, require('app').onError);
            },
            template: require('handlebars').compile(template),
            users: null,
            onPostSuccess: function (data) {
                Materialize.toast('User updated!', 4000);
            },
            onGetSuccess: function (data) {
                var app = require('app');

                var templateData = data.user;
                templateData.menu_html = app.getMenu();
                templateData.is_user = (app.getRole() == 'ROLE_USER');
                templateData.possible_roles = [
                    {id:'ROLE_USER', title:'Regular user', selected: (templateData.role == 'ROLE_USER')},
                    {id:'ROLE_MANAGER', title:'User manager', selected: (templateData.role == 'ROLE_MANAGER')},
                    {id:'ROLE_ADMIN', title:'Administrator', selected: (templateData.role == 'ROLE_ADMIN')}
                ];

                if(app.getRole() == 'ROLE_MANAGER'){
                    templateData.possible_roles = templateData.possible_roles.splice(-1,1)
                }

                var html = module.template(templateData);
                $('#app').hide().html(html).show('fast');
                module.bindActions();
            },
            onFormSubmit: function (e) {
                e.preventDefault();
                apiClient.updateUser(
                    module.userId,
                    $('#editUser__username').val(),
                    $('#editUser__role').val(),
                    $('#editUser__dailyNormal').val(),
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

                $('#editUser__date').pickadate(params);
                $('select').material_select();
                $('.editUser__form').on('submit', module.onFormSubmit);
                $('.editUser__deleteLink').click(module.deleteUser.onDeleteClick)

            },
            deleteUser: {
                onDeleteClick: function(e){
                    if (confirm('Are you sure you want to delete user from the database permanently?')) {
                        apiClient.deleteUser(module.userId, module.deleteUser.onSuccess, require('app').onError);
                    } else {
                        e.preventDefault();
                    }
                },
                onSuccess: function(data){
                    Materialize.toast('User deleted!', 4000);
                    location.hash = '#users_list';
                }
            }
        };

        return {
            init: module.init
        }
    }
);