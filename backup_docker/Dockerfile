FROM php:8.2-apache

# Extensões necessárias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Ativa o mod_rewrite do Apache
RUN a2enmod rewrite

# Copia o projeto para o Apache
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html

# Render usa a porta 10000
ENV PORT=10000

# Faz o Apache escutar na porta 10000
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

CMD ["apache2-foreground"]