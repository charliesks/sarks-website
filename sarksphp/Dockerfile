FROM phpmyadmin:latest

# Change Apache to listen on port 8081
RUN sed -i 's|<VirtualHost \*:80>|<VirtualHost *:8081>|' /etc/apache2/sites-enabled/000-default.conf \
 && sed -i '/^Listen /d' /etc/apache2/ports.conf \
 && echo 'Listen 8081' >> /etc/apache2/ports.conf

# Expose the new port
EXPOSE 8081
