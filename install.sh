#!/bin/bash

clear

stty erase '^?'

echo -n "Database Host (usually is localhost): "
read DBHOST
    
echo -n "Database Name: "
read DBNAME

echo -n "Database User: "
read DBUSER

echo -n "Database Password: "
read DBPASS

echo -n "Institution Name: "
read INST_NAME

echo -n "Institution Contact Email: "
read SYS_EMAIL

echo -n "eduTrac SIS Admin First Name: "
read ADMIN_FNAME

echo -n "eduTrac SIS Admin Last Name: "
read ADMIN_LNAME

echo -n "eduTrac SIS Admin Email: "
read ADMIN_EMAIL

echo -n "eduTrac SIS Admin Username: "
read ADMIN_USER

echo -n "eduTrac SIS URL (i.e. example.com): "
read URL

echo
echo "Copying files..."
echo

cp app/views/install/data/install-extend.sql .
cp config.sample.php config.php

echo
echo "Writing to files..."
echo

TODAY=$(date +%Y-%m-%d)
HOUR=$(date +%T)

sed -i "s|{uname}|$ADMIN_USER|g" install-extend.sql

sed -i "s|{pass}|\$2a\$08\$nG7Ba8WwACoM1zdb/RX.RuhfG8LBtesBCmO58TzJqPmxngknlKgtS|g" install-extend.sql

sed -i "s|{aemail}|$ADMIN_EMAIL|g" install-extend.sql

sed -i "s|{fname}|$ADMIN_FNAME|g" install-extend.sql

sed -i "s|{lname}|$ADMIN_LNAME|g" install-extend.sql

sed -i "s|{now}|$TODAY $HOUR|g" install-extend.sql

sed -i "s|{email}|$SYS_EMAIL|g" install-extend.sql

sed -i "s|{institutionname}|$INST_NAME|g" install-extend.sql

sed -i "s|{url}|$URL|g" install-extend.sql

sed -i "s|{addDate}|$TODAY|g" install-extend.sql

sed -i "s|{product}|eduTrac SIS|g" config.php

sed -i "s|{company}|7 Media Web Solutions, LLC|g" config.php

sed -i "s|{version}|6.2.8|g" config.php

sed -i "s|{datenow}|$TODAY $HOUR|g" config.php

sed -i "s|{hostname}|$DBHOST|g" config.php

sed -i "s|{database}|$DBNAME|g" config.php

sed -i "s|{username}|$DBUSER|g" config.php

sed -i "s|{password}|$DBPASS|g" config.php

sed -i "s|{siteurl}|$URL|g" config.php

sed -i "s|{institutionname}|$INST_NAME|g" config.php

echo
echo "Installing Database Tables..."
echo

mysql -h $DBHOST -u $DBUSER -p$DBPASS $DBNAME < install-extend.sql

echo
echo "Deleting Files..."
echo

rm -rf install-extend.sql

echo
echo "Finished installing eduTrac SIS"
echo

echo "+=================================================+"
echo "| eduTrac SIS LINKS"
echo "+=================================================+"
echo "|"
echo "| Install URL: http://$URL/"
echo "|"
echo "+=================================================+"
echo "| ADMIN ACCOUNT"
echo "+=================================================+"
echo "|"
echo "| Username: $ADMIN_USER"
echo "| Password: edutrac"
echo "|"
echo "+=================================================+"
echo "| DATABASE INFO"
echo "+=================================================+"
echo "|"
echo "| Database: $DBNAME"
echo "| Username: $DBUSER"
echo "| Password: $DBPASS"
echo "|"
echo "+=================================================+"
    
    exit
fi