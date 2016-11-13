define(function () {
    var module = {
        baseUrl: 'http://localhost:8000/v1/',
        authToken: '',
        login: function (username, password, success, fail) {
            var successHandler = function (data) {
                module.setToken(data.token);
                success(data);
            };

            module.request('login', {
                username: username,
                password: password
            }, 'post', successHandler, fail, true);
        },
        setToken: function (token) {
            module.authToken = token;
            require('app').saveToken(token);
        },
        addUser: function (username, password, success, fail) {
            module.request('register', {
                username: username,
                password: password
            }, 'post', success, fail, true);
        },
        register: function (username, password, success, fail) {
            var successHandler = function (data) {
                module.setToken(data.token);
                success(data);
            };

            module.request('register', {
                username: username,
                password: password
            }, 'post', successHandler, fail, true);
        },
        getNotes: function (dateFrom, dateTo, timeFrom, timeTo, page, success, fail) {
            module.request('notes', {
                page: page,
                from_date: dateFrom,
                to_date: dateTo,
                from_time: timeFrom,
                to_time: timeTo
            }, 'get', success, fail);
        },
        updateNote: function (id, text, date, time, calories, user_id, success, fail) {
            module.request('notes/' + id, {
                date: date,
                time: time,
                calories: calories,
                user_id: user_id,
                text: text
            }, 'PUT', success, fail);
        },
        updateUser: function (id, username, role, daily_normal, success, fail) {
            var data = {};
            if (typeof username !== 'undefined') {
                data.username = username;
            }
            if (typeof role !== 'undefined') {
                data.role = role;
            }
            if (typeof daily_normal !== 'undefined') {
                data.daily_normal = daily_normal;
            }

            module.request('users/' + id, data, 'PUT', success, fail);
        },
        getNote: function (id, success, fail) {
            module.request('notes/' + id, {}, 'get', success, fail);
        },
        getUser: function (id, success, fail) {
            module.request('users/' + id, {}, 'get', success, fail);
        },
        getUsers: function (success, fail) {
            module.request('users', {}, 'get', success, fail);
        },
        request: function (path, data, method, success, fail, skipHeaders) {
            var onRequestComplete = function (response, textStatus, xhr) {
                //for errors
                if (response.responseJSON) {
                    response = response.responseJSON;
                }
                console.log(response, textStatus, xhr);

                var successfulRequest = response.success;
                delete response.success;
                if (successfulRequest) {
                    success(response);
                } else {
                    console.log(path, data, method, success, fail, skipHeaders);

                    fail(response);
                }
            };

            method = method.toUpperCase();

            var ajaxSettings = {
                url: module.baseUrl + path,
                data: data,
                dataType: 'JSON',
                method: method
            };

            if (!skipHeaders) {
                ajaxSettings['headers'] = {'X-AUTH-TOKEN': module.authToken};
            }

            $.ajax(ajaxSettings).fail(onRequestComplete).done(onRequestComplete);
        },
        getLastCode: function () {
            return module.code;
        }
    };

    return {
        login: module.login,
        getUsers: module.getUsers,
        getNotes: module.getNotes,
        setToken: module.setToken,
        getNote: module.getNote,
        getUser: module.getUser,
        updateNote: module.updateNote,
        updateUser: module.updateUser,
        addUser: module.addUser,
        getLastCode: module.getLastCode,
        register: module.register
    };
});