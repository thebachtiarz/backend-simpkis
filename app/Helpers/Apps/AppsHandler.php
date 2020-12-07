<?php

/**
 * use libraries
 */

/**
 * use models
 *
 */

/** */

/**
 * handler template theme asset
 *
 * @return string
 */
# online version
function online_asset(): string
{
    return 'http://bachtiars.com/AdminLTE-3.0.2/';
}

# offline version
function offline_asset(): string
{
    return asset('AdminLTE-3.0.2');
}

/**
 * icon apps
 *
 * @return string
 */
function apps_icon(): string
{
    return online_asset() . '/dist/img/AdminLTELogo.png';
}

/**
 * default url
 * user profile image
 *
 * @return string
 */
function default_url_user_image(): string
{
    return '/files/image/profile/users/';
}

/**
 * default user image
 *
 * @return string
 */
function default_user_image(): string
{
    return '/files/image/profile/default.jpg';
}

/**
 * get ip address client
 *
 * @return string
 */
function getClientIpAddress(): string
{
    return request()->ip();
}
