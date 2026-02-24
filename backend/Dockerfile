FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar el backend al directorio de trabajo
COPY . /var/www/html/

# Establecer permisos (opcional, ajusta según necesidad)
RUN chown -R www-data:www-data /var/www/html
