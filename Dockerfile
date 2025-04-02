# Use official PHP image with Apache
FROM  quay.io/csarkis/sarks-website 

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring zip mysqli pdo pdo_mysql

# Enable Apache mod_rewrite (important for frameworks like Laravel, WordPress, etc.)
RUN a2enmod rewrite

#Change Apache config to listen on port 8080 instead of 80
RUN sed -i 's|<VirtualHost \*:80>|<VirtualHost *:8080>|' /etc/apache2/sites-enabled/000-default.conf 

#RUN sed -i 's|<VirtualHost \*:80>|<VirtualHost *:8080>|' /etc/apache2/sites-enabled/000-default.conf \
 #&& echo "Listen 8080" >> /etc/apache2/ports.conf

# Copy website files to Apache root
COPY ./sarks /var/www/html/

# Set the working directory
WORKDIR /var/www/html/

# Change permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (Apache)
EXPOSE 8080
