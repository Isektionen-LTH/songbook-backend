FROM alpine

RUN apk add --no-cache apache2 php7-apache2 php7-session php7-curl php7-pdo_mysql php7-dom php7-fileinfo php7-simplexml php7-xml php7-xmlreader php7-xmlwriter phpmyadmin mariadb mariadb-client composer

# Set up apache
RUN sed -i "s/#LoadModule rewrite_module modules\/mod_rewrite.so/LoadModule rewrite_module modules\/mod_rewrite.so/" /etc/apache2/httpd.conf
RUN sed -i "s/#ServerName www.example.com:80/ServerName songbook.local:80/" /etc/apache2/httpd.conf
RUN sed -i "s/DocumentRoot \"\/var\/www\/localhost\/htdocs\"/DocumentRoot \"\/app\/src\"/" /etc/apache2/httpd.conf
RUN sed -i "s/<Directory \"\/var\/www\/localhost\/htdocs\">/<Directory \"\/app\/src\">/" /etc/apache2/httpd.conf
RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/httpd.conf
RUN sed -i "s/LogLevel warn/LogLevel debug/g" /etc/apache2/httpd.conf

# PHP config
RUN sed -i "s/display_errors = Off/display_errors = On/" /etc/php7/php.ini
RUN sed -i "s/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/error_reporting = E_ALL/" /etc/php7/php.ini
RUN sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 8M/" /etc/php7/php.ini

# phpMyAdmin config
RUN sed -i "s/AllowNoPassword'\] = false;/AllowNoPassword'\] = true;/" /etc/phpmyadmin/config.inc.php
RUN chown apache:apache /etc/phpmyadmin/config.inc.php
RUN chown apache:apache -R /usr/share/webapps/phpmyadmin

# Install the run script
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
