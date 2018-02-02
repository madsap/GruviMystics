#!/usr/bin/env bash

# some other niceties could be found here:
# https://www.snip2code.com/Snippet/16602/Vagrant-provision-script-for-php--Apache

# update / upgrade
sudo apt update
sudo apt -y upgrade

# install MySQL
sudo echo "mysql-server mysql-server/root_password password JifkGH3Jdsoiglgfsd" | sudo debconf-set-selections
sudo echo "mysql-server mysql-server/root_password_again password JifkGH3Jdsoiglgfsd" | sudo debconf-set-selections
sudo apt -y install mysql-server

mysql -u root -pJifkGH3Jdsoiglgfsd -e "CREATE DATABASE gruvi_db; \
    CREATE USER gruvi_us@'%' IDENTIFIED BY 'hJFyyrHOFjjhHGSGFKKOO2354'; \
    GRANT ALL PRIVILEGES ON *.* TO gruvi_us@'%' IDENTIFIED BY 'hJFyyrHOFjjhHGSGFKKOO2354' \
    WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0; \
    GRANT ALL PRIVILEGES ON gruvi_db.* TO gruvi_us@'%';"

# allow access from any ip (only DEV)
sudo sed -i 's/^bind-address.*127\.0\.0\.1/bind-address = 0\.0\.0\.0/g' /etc/mysql/mysql.conf.d/mysqld.cnf

# fill /etc/crontab
sudo sed -i 's/\/etc\/cron\.monthly )/\/etc\/cron\.monthly )\n \* \*    \* \* \*   root    php \/var\/www\/mdh\/app\/yii cron\/run/g' /etc/crontab

# install apache2 and other tools
sudo apt -y install apache2 \
            vim \
            htop \
            mc \
            unzip \
            git \
            curl

# install php and php-extensions
sudo apt -y install php \
            libapache2-mod-php \
            php-mcrypt \
            php-mysql \
            php-xml \
            php-curl \
            php-mbstring \
            libapache2-mod-xsendfile \
            php-gd \
            php-bcmath

sudo service apache2 restart

sudo service mysql restart

sudo apt -y autoremove

sudo apt clean

# setup hosts file
VHOST=$(cat <<EOF
<VirtualHost *:80>
    DocumentRoot "/var/www/gruvi/web/"
    XSendFile On
    XSendFilePath /var/www/gruvi/data/
    <Directory "/var/www/">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-available/000-default.conf

# enable mod_rewrite
sudo a2enmod rewrite

# create local config file (for PHP)
cp /var/www/gruvi/config/_localConfig.php.default /var/www/gruvi/config/_localConfig.php

cp /var/www/gruvi/yii.default /var/www/gruvi/yii

cp /var/www/gruvi/web/index.php.default /var/www/gruvi/web/index.php

# install Composer
sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin \
    && sudo ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

sudo chown vagrant:vagrant -R /home/vagrant/.composer

# install Composer plugin
cd /var/www/gruvi/ \
    && composer global require "fxp/composer-asset-plugin:^1.2.0" \
    && composer install

# migrate DB
php yii migrate/up 0 --interactive=0

# restart apache
sudo service apache2 restart