define(function () {
    var module = {
        baseUrl: 'http://localhost:8000/v1/',
        authToken: '',
        login: function (username, password, success, fail) {
            var successHandler = function (data) {
                module.authToken = data.token;
                success(data);
            };

            module.request('login', {
                username: username,
                password: password
            }, 'post', successHandler, fail, true);
        },
        register: function (username, password, success, fail) {
            var successHandler = function (data) {
                module.authToken = data.token;
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
                user_id: user_id
            }, 'PUT', success, fail);
        },
        getNote: function (id, success, fail) {
            module.request('notes/' + id, {}, 'get', success, fail);
        },
        getUsers: function (success, fail) {
            module.request('users', {}, 'get', function (data) {
                success(data.users);
            }, fail);
        },
        request: function (path, data, method, success, fail, skipHeaders) {
            var onRequestComplete = function (response) {
                if (response.responseJSON) {
                    response = response.responseJSON;
                }

                var successfulRequest = response.success;
                delete response.success;
                if (successfulRequest) {
                    success(response);
                } else {
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
        }
    };

    return {
        login: module.login,
        getUsers: module.getUsers,
        getNotes: module.getNotes,
        getNote: module.getNote,
        updateNote: module.updateNote,
        register: module.register
    };
});