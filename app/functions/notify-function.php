<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;

/**
 * eduTrac SIS Desktop Notification Functions
 *
 * @license GPLv3
 *         
 * @since 6.2.11
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Styles for desktop notification.
 * 
 * @since 6.2.11
 */
function etsis_notify_style()
{
    $style = '<link href="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.css" rel="stylesheet" type="text/css" />' . "\n";
    $style .= '<link href="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.brighttheme.css" rel="stylesheet" type="text/css" />' . "\n";
    $style .= '<link href="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.buttons.css" rel="stylesheet" type="text/css" />' . "\n";
    $style .= '<link href="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.nonblock.css" rel="stylesheet" type="text/css" />' . "\n";
    $style .= '<link href="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.mobile.css" rel="stylesheet" type="text/css" />' . "\n";
    $style .= '<link href="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.history.css" rel="stylesheet" type="text/css" />' . "\n";
    echo $style;
}

/**
 * Scripts for desktop notification.
 * 
 * @since 6.2.11
 */
function etsis_notify_script()
{
    $script = '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.js"></script>' . "\n";
    $script .= '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.animate.js"></script>' . "\n";
    $script .= '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.buttons.js"></script>' . "\n";
    $script .= '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.confirm.js"></script>' . "\n";
    $script .= '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.nonblock.js"></script>' . "\n";
    $script .= '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.mobile.js"></script>' . "\n";
    $script .= '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.desktop.js"></script>' . "\n";
    $script .= '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.history.js"></script>' . "\n";
    $script .= '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.callbacks.js"></script>' . "\n";
    $script .= '<script type="text/javascript" src="' . get_base_url() . 'static/assets/plugins/pnotify/src/pnotify.reference.js"></script>' . "\n";
    echo $script;
}

/**
 * Desktop Notifications
 * 
 * Used to pass notifications to the desktop.
 * 
 * @since 6.2.11
 * @param string $title Give title of notification.
 * @param string $message Message that should be displayed.
 * @param bool $hide True if notification hides automatically.
 */
function etsis_desktop_notify($title, $message, $hide = 'true')
{
    $app = \Liten\Liten::getInstance();
    $script = "<script type=\"text/javascript\">
                $(function(){
                    PNotify.desktop.permission();
                    (new PNotify({
                        title: '$title',
                        text: '$message',
                        addclass: 'growl',
                        styling: 'fontawesome',
                        width: \"400px\",
                        type: \"notice\",
                        shadow: true,
                        hide: $hide,
                        delay: 200000,
                        desktop: {
                            desktop: true,
                            fallback: true,
                            icon: '" . get_school_photo(get_persondata('personID'), get_persondata('email')) . "'
                        },
                        mobile: {
                            swipe_dismiss: true,
                            styling: true
                        }
                    }));
                });
            </script>";
    return $app->hook->apply_filter('pnotify', $app->flash('pnotify', $script));
}

/**
 * Desktop Push Notification
 * 
 * Notifications that can be pushed at a delayed time.
 * 
 * @since 6.2.11
 * @param string $title Give title of notification.
 * @param string $message Message that should be displayed.
 */
function etsis_push_notify($title, $message)
{
    $app = \Liten\Liten::getInstance();
    // Create a Notifier
    $notifier = NotifierFactory::create();

    // Create your notification
    $notification = (new Notification())
        ->setTitle($title)
        ->setBody($message)
        ->setIcon(BASE_PATH . 'static/assets/imgages/icon-success.png');

    // Send it
    return $app->hook->apply_filter('push_notify', $notifier->send($notification));
}
