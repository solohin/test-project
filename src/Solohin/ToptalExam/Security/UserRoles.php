<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 04/11/16
 * Time: 03:09
 */

namespace Solohin\ToptalExam\Security;

class UserRoles
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_USER = 'ROLE_USER';

    const DEFAULT_ROLE = self::ROLE_USER;
}