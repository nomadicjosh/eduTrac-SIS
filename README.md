# eduTrac SIS

eduTrac SIS is an advanced open source student information system for higher education.

## System Requirement

* Minimum of PHP version 5.6x
* Apache / Nginx
* Gettext enabled


## Features

* Manage Students
* Manage Human Resources
* Manage Staff
* Manage Faculty
* Manage Courses
* Manage Course Sections
* Manage Roles / Permissions
* Manage Students / Records
* Print Course Catalogs
* Print Section Rosters
* SQL Interface
* Manage Applications
* Enter final grades for GPA calculation
* Graduate Students
* Generate Transcripts
* Set Business Rules
* Plugin API
* RESTful API
* and much more...

## Rewrite

### Apache

<pre>
RewriteEngine On
 
# Some hosts may require you to use the `RewriteBase` directive.
# If you need to use the `RewriteBase` directive, it should be the
# absolute physical path to the directory that contains this htaccess file.
#
# RewriteBase /
 
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
</pre>

### Nginx

#### Root Directory

<pre>
location / {
    try_files $uri /index.php$is_args$args;
}
</pre>

#### Subdirectory

<pre>
location /sis {
    try_files $uri /sis/index.php$is_args$args;
}
</pre>

## Resources.

* Take eduTrac SIS for a test drive by checking out the full featured [demo](http://demo.etsis.us/).
* Check out the [Youtube channel](https://www.youtube.com/channel/UC0Xg37nDDHh-_9bzCAUz6HQ/videos?view=0&sort=dd&shelf_id=0) for tips on using eduTrac SIS.
* [Online User's Manual](https://www.edutracsis.com/): everything you need to get eduTrac SIS installed and setup.
* Bug fixing: contribute by helping to squash [bugs](https://github.com/parkerj/eduTrac-SIS/issues)
* Handbook: majority of the classes, functions, methods and hooks are documented in the [developer's handbook](https://developer.edutracsis.com); contribute to it or use it to write plugins for the community.
* Translate eduTrac SIS into your [language](https://translate.edutracsis.com/)