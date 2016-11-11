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
        request: function (path, data, method, success, fail, skipHeaders) {
            var onRequestComplete = function (data) {
                var successfulRequest = data.success;
                delete data.success;
                if (successfulRequest) {
                    success(data);
                } else {
                    fail(data);
                }
            };

            var ajaxSettings = {
                url: module.baseUrl + path,
                data: data,
                dataType: 'JSON',
                method: method
            };

            if (!skipHeaders) {
                ajaxSettings['headers'] = {'X-AUTH-TOKEN': module.authToken};
            }

            $.ajax(ajaxSettings).always(onRequestComplete);
        }
    };

    return {
        login: module.login,
        getNotes: module.getNotes,
        register: module.register
    };
});