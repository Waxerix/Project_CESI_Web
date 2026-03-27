FROM php:8.2-apache
 
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite
 
RUN sed -i 's!/var/www/sesomate.local/Project_CESI_Web!/var/www/sesomate.local/Project_CESI_Web/public!g' /etc/apache2/sites-available/000-default.conf
 
WORKDIR /var/www/sesomate.local/Project_CESI_Web