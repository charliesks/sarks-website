FROM quay.io/csarkis/sarks-php

# Change Apache to listen on port 8081
RUN sed -i 's|<VirtualHost \*:80>|<VirtualHost *:8081>|' /etc/apache2/sites-enabled/000-default.conf \
    && sed -i '/^Listen /d' /etc/apache2/ports.conf \
    && echo 'Listen 8081' >> /etc/apache2/ports.conf

# Disable loading of config.secret.inc.php
RUN sed -i '/config\.secret\.inc\.php/s/^/\/\//' /etc/phpmyadmin/config.inc.php

EXPOSE 8081
