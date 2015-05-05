(function () {

    // SPRO screen
    Mousetrap.bind(['alt+shift+s'], function() {
        window.location = rootPath + 'student/';
        return false;
    });
    
    // NAE screen
    Mousetrap.bind(['command+shift+a','ctrl+shift+a'], function() {
        window.location = rootPath + 'person/';
        return false;
    });
    
    // PROG screen
    Mousetrap.bind(['command+shift+p','ctrl+shift+p'], function() {
        window.location = rootPath + 'program/';
        return false;
    });
    
    // SECT screen
    Mousetrap.bind(['alt+shift+x'], function() {
        window.location = rootPath + 'section/';
        return false;
    });
    
    // CRSE screen
    Mousetrap.bind(['command+shift+r','ctrl+shift+r'], function() {
        window.location = rootPath + 'course/';
        return false;
    });
    
    // RGN screen
    Mousetrap.bind(['command+alt+r', 'ctrl+alt+r'], function() {
        window.location = rootPath + 'section/register/';
        return false;
    });
    
    // BRGN screen
    Mousetrap.bind(['command+alt+b', 'ctrl+alt+b'], function() {
        window.location = rootPath + 'section/batch_register/';
        return false;
    });
    
    // STAFF screen
    Mousetrap.bind(['command+alt+s', 'ctrl+alt+s'], function() {
        window.location = rootPath + 'staff/';
        return false;
    });
    
    // APER screen
    Mousetrap.bind(['alt+shift+a'], function() {
        window.location = rootPath + 'person/add/';
        return false;
    });
    
    // APRG screen
    Mousetrap.bind(['alt+shift+p'], function() {
        window.location = rootPath + 'program/add/';
        return false;
    });
    
    // CRSE screen
    Mousetrap.bind(['alt+shift+r'], function() {
        window.location = rootPath + 'course/add/';
        return false;
    });
    
    // Close a screen and return to the dashboard
    Mousetrap.bind(['alt+x'], function() {
        window.location = rootPath + 'dashboard/';
        return false;
    });
    
}) ();