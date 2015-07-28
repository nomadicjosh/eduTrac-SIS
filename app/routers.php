<?php

/**
 * This file is used for lazy loading of the routers
 * and modules when called.
 */

if (strpos(getPathInfo('/api'), "/api") === 0)
{
    require($app->config('routers_dir') . 'api.router.php');
}

elseif (strpos(getPathInfo('/dashboard'), "/dashboard") === 0)
{
    require($app->config('routers_dir') . 'dashboard.router.php');
}

elseif (strpos(getPathInfo('/appl'), "/appl") === 0)
{
    require($app->config('routers_dir') . 'appl.router.php');
}

elseif (strpos(getPathInfo('/calendar'), "/calendar") === 0)
{
    require($app->config('routers_dir') . 'booking.router.php');
}

elseif (strpos(getPathInfo('/cmgmt'), "/cmgmt") === 0)
{
    require($app->config('routers_dir') . 'cmgmt.router.php');
}

elseif (strpos(getPathInfo('/crse'), "/crse") === 0)
{
    require($app->config('routers_dir') . 'course.router.php');
    
    if(file_exists($app->config('routers_dir') . 'transfer.router.php')) {
        require($app->config('routers_dir') . 'transfer.router.php');
    }
}

elseif (strpos(getPathInfo('/courses'), "/courses") === 0)
{
    require($app->config('routers_dir') . 'courses.router.php');
}

elseif (strpos(getPathInfo('/cron'), "/cron") === 0)
{
    require($app->config('routers_dir') . 'cron.router.php');
}

elseif (strpos(getPathInfo('/financial'), "/financial") === 0)
{
    require($app->config('routers_dir') . 'financial.router.php');
}

elseif (strpos(getPathInfo('/form'), "/form") === 0)
{
    require($app->config('routers_dir') . 'form.router.php');
    
    if(file_exists($app->config('routers_dir') . 'import.router.php')) {
        require($app->config('routers_dir') . 'import.router.php');
    }
    
    if(file_exists($app->config('routers_dir') . 'booking.router.php')) {
        require($app->config('routers_dir') . 'booking.router.php');
    }
    
    if(file_exists($app->config('routers_dir') . 'myet.router.php')) {
        require($app->config('routers_dir') . 'myet.router.php');
    }
}

elseif (strpos(getPathInfo('/hr'), "/hr") === 0)
{
    require($app->config('routers_dir') . 'hr.router.php');
    
    if(file_exists($app->config('routers_dir') . 'timesheet.router.php')) {
        require($app->config('routers_dir') . 'timesheet.router.php');
    }
}

elseif (strpos(getPathInfo('/nae'), "/nae") === 0)
{
    require($app->config('routers_dir') . 'person.router.php');
}

elseif (strpos(getPathInfo('/plugins'), "/plugins") === 0)
{
    require($app->config('routers_dir') . 'plugins.router.php');
}

elseif (strpos(getPathInfo('/program'), "/program") === 0)
{
    require($app->config('routers_dir') . 'program.router.php');
}

elseif (strpos(getPathInfo('/sect/brgn'), "/sect/brgn") === 0)
{
    require($app->config('routers_dir') . 'savedquery.router.php');
}

elseif (strpos(getPathInfo('/sect'), "/sect") === 0)
{
    require($app->config('routers_dir') . 'section.router.php');
    
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

elseif (strpos(getPathInfo('/admin'), "/admin") === 0)
{
    require($app->config('routers_dir') . 'myet.router.php');
}

elseif (strpos(getPathInfo('/setting'), "/setting") === 0)
{
    require($app->config('routers_dir') . 'settings.router.php');
}

elseif (strpos(getPathInfo('/email'), "/email") === 0)
{
    require($app->config('routers_dir') . 'settings.router.php');
}

elseif (strpos(getPathInfo('/registration'), "/registration") === 0)
{
    require($app->config('routers_dir') . 'settings.router.php');
}

elseif (strpos(getPathInfo('/templates'), "/templates") === 0)
{
    require($app->config('routers_dir') . 'settings.router.php');
}

elseif (strpos(getPathInfo('/sql'), "/sql") === 0)
{
    require($app->config('routers_dir') . 'sql.router.php');
    
    if(file_exists($app->config('routers_dir') . 'savedquery.router.php')) {
        require($app->config('routers_dir') . 'savedquery.router.php');
    }
}

elseif (strpos(getPathInfo('/staff'), "/staff") === 0)
{
    require($app->config('routers_dir') . 'staff.router.php');
    
    if(file_exists($app->config('routers_dir') . 'timesheet.router.php')) {
        require($app->config('routers_dir') . 'timesheet.router.php');
    }
}

elseif (strpos(getPathInfo('/stu'), "/stu") === 0)
{
    require($app->config('routers_dir') . 'student.router.php');
    
    if(file_exists($app->config('routers_dir') . 'financial.router.php')) {
        require($app->config('routers_dir') . 'financial.router.php');
    }
    
    if(file_exists($app->config('routers_dir') . 'gradebook.router.php')) {
        require($app->config('routers_dir') . 'gradebook.router.php');
    }
}

elseif (strpos(getPathInfo('/err'), "/err") === 0)
{
    require($app->config('routers_dir') . 'error.router.php');
    
    if(file_exists($app->config('routers_dir') . 'log.router.php')) {
        require($app->config('routers_dir') . 'log.router.php');
    }
}

elseif (strpos(getPathInfo('/audit-trail'), "/audit-trail") === 0)
{
    require($app->config('routers_dir') . 'log.router.php');
}

else {
    require($app->config('routers_dir') . 'index.router.php');
    
    if(file_exists($app->config('routers_dir') . 'myet.router.php')) {
        require($app->config('routers_dir') . 'myet.router.php');
    } // default routes
}

