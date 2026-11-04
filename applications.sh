#!/usr/bin/env bash

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

#install mysql apache php phpmyadmin with default passwd root. You can run dpkg-reconfigure to change  
export DEBIAN_FRONTEND=noninteractive
debconf-set-selections /vagrant/help_config/debconf.txt
apt-get install -y mysql-server
apt-get install -y phpmyadmin

#copy profile
cp /vagrant/help_config/profile /home/vagrant/.profile
tr -d 'r' < /home/vagrant/.profile > /home/vagrant/.profile

#set up diplomatiki
#1. create appropriate .conf for apache2 HOST & VERSION SPECIFIC!!!! & then restart apache
ln -s /vagrant/project/setup/diplomatiki.conf /etc/apache2/conf.d/
apachectl restart
#2. set up mysql user and database
mysql -uroot -proot -e'source /vagrant/project/setup/user.sql'
#3. create tables
mysql -usentiment -pJohnSnow sentiment -e'source /vagrant/project/setup/sentiment.sql'
#4. populate users table with data
mysql -usentiment -pJohnSnow sentiment -e'source /vagrant/project/setup/users-data.sql'
#5. populate with sample data 
mysql -usentiment -pJohnSnow sentiment -e'source /vagrant/project/setup/sentiment-sample-data.sql'

