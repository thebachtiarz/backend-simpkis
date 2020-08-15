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
 * @return void
 */
# online version
function online_asset()
{
    return 'http://bachtiars.com/AdminLTE-3.0.2/';
}
# offline version
function offline_asset()
{
    return asset('AdminLTE-3.0.2');
}

/**
 * icon apps
 *
 * @return void
 */
function apps_icon()
{
    return online_asset() . '/dist/img/AdminLTELogo.png';
}

/**
 * default url
 * user profile image
 *
 * @return void
 */
function default_url_user_image()
{
    return '/files/image/profile/users/';
}

/**
 * default user image
 *
 * @return void
 */
function default_user_image()
{
    return '/files/image/profile/default.jpg';
}

/**
 * get ip address client
 *
 * @return void
 */
function getClientIpAddress()
{
    return request()->ip();
}
