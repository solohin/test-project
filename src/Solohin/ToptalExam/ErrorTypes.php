<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 04/11/16
 * Time: 18:17
 */

namespace Solohin\ToptalExam;

class ErrorTypes
{
    const INTERNAL_ERROR = 'internal_error';

    const WRONG_PASSWORD = 'wrong_password';
    const WRONG_USERNAME = 'wrong_username';
    const USERNAME_EXISTS = 'username_exists';

    const SHORT_USERNAME = 'short_username';
    const LONG_USERNAME = 'long_username';
    const SHORT_PASSWORD = 'short_password';
    const LONG_PASSWORD = 'long_password';

    const METHOD_NOT_FOUND = 'method_not_found';
    const WRONG_TOKEN = 'wrong_token';
    const NO_TOKEN = 'no_token';

    const NOTE_NOT_FOUND = 'note_not_found';
    const USER_NOT_FOUND = 'user_not_found';
    const EMPTY_PARAMETERS = 'empty_parameters';

    const PERMISSION_DENIED = 'permission_denied';
    const EMPTY_USER_ID = 'empty_user_id';
    const WRONG_DATE_FORMAT = 'wrong_date_format';
    const WRONG_TIME_FORMAT = 'wrong_time_format';
}