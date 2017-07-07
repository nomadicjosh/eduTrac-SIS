<?php

/**
 * This file is used for lazy loading of the routers
 * and modules when called.
 */

if (strpos(get_path_info('/api'), "/api") === 0)
{
    require($app->config('routers_dir') . 'api.router.php');
}

elseif (strpos(get_path_info('/dashboard'), "/dashboard") === 0)
{
    _etsis_dashboard_router();
}

elseif (strpos(get_path_info('/appl'), "/appl") === 0)
{
    _etsis_appl_router();
}

elseif (strpos(get_path_info('/calendar'), "/calendar") === 0)
{
    require($app->config('routers_dir') . 'booking.router.php');
}

elseif (strpos(get_path_info('/mrkt'), "/mrkt") === 0)
{
    if(file_exists($app->config('routers_dir') . 'mrkt.router.php')) {
        require($app->config('routers_dir') . 'mrkt.router.php');
    }
}

elseif (strpos(get_path_info('/crse'), "/crse") === 0)
{    
    _etsis_crse_router();
    
    if(file_exists($app->config('routers_dir') . 'transfer.router.php')) {
        require($app->config('routers_dir') . 'transfer.router.php');
    }
}

elseif (strpos(get_path_info('/courses'), "/courses") === 0)
{
    require($app->config('routers_dir') . 'courses.router.php');
}

elseif (strpos(get_path_info('/cron'), "/cron") === 0)
{
    require($app->config('routers_dir') . 'cron.router.php');
}

elseif (strpos(get_path_info('/financial'), "/financial") === 0)
{
    require($app->config('routers_dir') . 'financial.router.php');
}

elseif (strpos(get_path_info('/form'), "/form") === 0)
{
    require($app->config('routers_dir') . 'form.router.php');
    
    if(file_exists($app->config('routers_dir') . 'import.router.php')) {
        require($app->config('routers_dir') . 'import.router.php');
    }
    
    if(file_exists($app->config('routers_dir') . 'booking.router.php')) {
        require($app->config('routers_dir') . 'booking.router.php');
    }
    
    if(file_exists($app->config('routers_dir') . 'myetsis.router.php')) {
        _etsis_myetsis_router();
    }
}

elseif (strpos(get_path_info('/hr'), "/hr") === 0)
{
    require($app->config('routers_dir') . 'hr.router.php');
    
    if(file_exists($app->config('routers_dir') . 'timesheet.router.php')) {
        require($app->config('routers_dir') . 'timesheet.router.php');
    }
}

elseif (strpos(get_path_info('/nae'), "/nae") === 0)
{
    _etsis_nae_router();
}

elseif (strpos(get_path_info('/plugins'), "/plugins") === 0)
{
    require($app->config('routers_dir') . 'plugins.router.php');
}

elseif (strpos(get_path_info('/program'), "/program") === 0)
{
    _etsis_prog_router();
}

elseif (strpos(get_path_info('/sect/brgn'), "/sect/brgn") === 0)
{
    require($app->config('routers_dir') . 'savedquery.router.php');
}

elseif (strpos(get_path_info('/sect'), "/sect") === 0)
{
    _etsis_sect_router();
    
    if(file_exists($app->config('routers_dir') . 'gradebook.router.php')) {
        require($app->config('routers_dir') . 'gradebook.router.php');
    }
    
    if(file_exists($app->config('routers_dir') . 'booking.router.php')) {
        require($app->config('routers_dir') . 'booking.router.php');
    }
    
    if(file_exists($app->config('routers_dir') . 'financial.router.php')) {
        require($app->config('routers_dir') . 'financial.router.php');
    }
}

elseif (strpos(get_path_info('/admin'), "/admin") === 0)
{
    _etsis_myetsis_router();
}

elseif (strpos(get_path_info('/setting'), "/setting") === 0)
{
    require($app->config('routers_dir') . 'settings.router.php');
}

elseif (strpos(get_path_info('/email'), "/email") === 0)
{
    require($app->config('routers_dir') . 'settings.router.php');
}

elseif (strpos(get_path_info('/registration'), "/registration") === 0)
{
    require($app->config('routers_dir') . 'settings.router.php');
}

elseif (strpos(get_path_info('/templates'), "/templates") === 0)
{
    require($app->config('routers_dir') . 'settings.router.php');
}

elseif (strpos(get_path_info('/sms'), "/sms") === 0)
{
    require($app->config('routers_dir') . 'settings.router.php');
}

elseif (strpos(get_path_info('/sql'), "/sql") === 0)
{
    require($app->config('routers_dir') . 'sql.router.php');
    
    if(file_exists($app->config('routers_dir') . 'savedquery.router.php')) {
        require($app->config('routers_dir') . 'savedquery.router.php');
    }
}

elseif (strpos(get_path_info('/staff'), "/staff") === 0)
{
    require($app->config('routers_dir') . 'staff.router.php');
    
    if(file_exists($app->config('routers_dir') . 'timesheet.router.php')) {
        require($app->config('routers_dir') . 'timesheet.router.php');
    }
}

elseif (strpos(get_path_info('/stu'), "/stu") === 0)
{
    _etsis_student_router();
    
    if(file_exists($app->config('routers_dir') . 'financial.router.php')) {
        require($app->config('routers_dir') . 'financial.router.php');
    }
    
    if(file_exists($app->config('routers_dir') . 'gradebook.router.php')) {
        require($app->config('routers_dir') . 'gradebook.router.php');
    }
}

elseif (strpos(get_path_info('/err'), "/err") === 0)
{
    require($app->config('routers_dir') . 'error.router.php');
    
    if(file_exists($app->config('routers_dir') . 'log.router.php')) {
        require($app->config('routers_dir') . 'log.router.php');
    }
}

elseif (strpos(get_path_info('/audit-trail'), "/audit-trail") === 0)
{
    require($app->config('routers_dir') . 'log.router.php');
}

elseif (strpos(get_path_info('/rlde'), "/rlde") === 0)
{
    require($app->config('routers_dir') . 'rlde.router.php');
}

else {
    _etsis_index_router();
    if(file_exists($app->config('routers_dir') . 'mrkt.router.php')) {
        require($app->config('routers_dir') . 'mrkt.router.php');
    }
    if(file_exists($app->config('routers_dir') . 'myetsis.router.php')) {
        _etsis_myetsis_router();
    } // default routes
}

