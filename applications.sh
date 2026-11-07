#!/usr/bin/env bash
#
# Provisioning script for the diplomatiki Vagrant VM.
#
# Credentials are NOT stored in this repository. They come from the .env file
# on the host (see .env.example), which the Vagrantfile passes in as
# environment variables:
#   MYSQL_ROOT_PASSWORD  MySQL root password (also used to log in to phpMyAdmin)
#   DB_NAME              application database name
#   DB_USER              application database user
#   DB_PASSWORD          application database user's password

#avoid interactive prompts from apt-get / dpkg-reconfigure
export DEBIAN_FRONTEND=noninteractive

#check the credentials we were given before touching anything
for var in MYSQL_ROOT_PASSWORD DB_NAME DB_USER DB_PASSWORD; do
	value="${!var}"
	if [ -z "$value" ]; then
		echo "ERROR: $var is not set. Create .env from .env.example on the host and run 'vagrant provision'." >&2
		exit 1
	fi
	#the values are written into SQL, PHP and debconf, so keep them to safe characters
	if ! [[ "$value" =~ ^[A-Za-z0-9._-]+$ ]]; then
		echo "ERROR: $var may only contain letters, digits, '.', '_' and '-'." >&2
		exit 1
	fi
done

apt-get update

#install vim --my favorite editor
apt-get install -y vim
#install byobu --very handy screen alternative
apt-get install -y byobu
#instal debconf-utils -- just in case something else needs it
apt-get install -y debconf-utils

#install greek
apt-get install -y language-pack-el
locale-gen el_GR
locale-gen el_GR.UTF-8
dpkg-reconfigure locales

#install mysql apache php phpmyadmin. Passwords come from .env; run dpkg-reconfigure to change them later
#phpMyAdmin's internal control user gets its own random password, nobody needs to know it
PMA_APP_PASSWORD="$(head -c 16 /dev/urandom | od -An -tx1 | tr -d ' \n')"
debconf-set-selections <<EOF
mysql-server-5.5 mysql-server/root_password password $MYSQL_ROOT_PASSWORD
mysql-server-5.5 mysql-server/root_password_again password $MYSQL_ROOT_PASSWORD
phpmyadmin phpmyadmin/dbconfig-install boolean true
phpmyadmin phpmyadmin/app-password-confirm password $PMA_APP_PASSWORD
phpmyadmin phpmyadmin/mysql/admin-pass password $MYSQL_ROOT_PASSWORD
phpmyadmin phpmyadmin/mysql/app-pass password $PMA_APP_PASSWORD
phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2
EOF
apt-get install -y mysql-server
apt-get install -y phpmyadmin
#PDO MySQL driver used by the site (normally pulled in by phpmyadmin, installed explicitly to be sure)
apt-get install -y php5-mysql

#copy profile, stripping Windows (CRLF) line endings
tr -d '\r' < /vagrant/help_config/profile > /home/vagrant/.profile

#set up diplomatiki
#1. create appropriate .conf for apache2 HOST & VERSION SPECIFIC!!!! & then restart apache
ln -sf /vagrant/project/setup/diplomatiki.conf /etc/apache2/conf.d/
apachectl restart

#2. write the DB settings for the PHP site outside the repo (/vagrant is shared with the host)
#   readable only by root and apache (www-data)
mkdir -p /etc/diplomatiki
cat > /etc/diplomatiki/db.php <<EOF
<?php
define('DBHOST', 'localhost');
define('DBUSER', '$DB_USER');
define('DBPASS', '$DB_PASSWORD');
define('DBNAME', '$DB_NAME');
EOF
chown root:www-data /etc/diplomatiki/db.php
chmod 640 /etc/diplomatiki/db.php

#3. mysql credentials go in temporary option files (mode 600), not on the command line,
#   so they don't show up in `ps` and get removed when the script ends
umask 077
ROOT_CNF="$(mktemp)"
APP_CNF="$(mktemp)"
trap 'rm -f "$ROOT_CNF" "$APP_CNF"' EXIT
printf '[client]\nuser=root\npassword=%s\n' "$MYSQL_ROOT_PASSWORD" > "$ROOT_CNF"
printf '[client]\nuser=%s\npassword=%s\n' "$DB_USER" "$DB_PASSWORD" > "$APP_CNF"
umask 022

#4. set up mysql user and database (GRANT ... IDENTIFIED BY creates the user, or updates its password)
mysql --defaults-extra-file="$ROOT_CNF" <<EOF
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
FLUSH PRIVILEGES;
EOF
#5. create tables
mysql --defaults-extra-file="$APP_CNF" "$DB_NAME" < /vagrant/project/setup/sentiment.sql
#6. populate users table with data
mysql --defaults-extra-file="$APP_CNF" "$DB_NAME" < /vagrant/project/setup/users-data.sql
#7. populate with sample data
mysql --defaults-extra-file="$APP_CNF" "$DB_NAME" < /vagrant/project/setup/sentiment-sample-data.sql
