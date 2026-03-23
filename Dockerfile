# Imagen base de PHP con Apache
FROM php:8.1-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copiar el código de tu aplicación
COPY . /var/www/html/

# Habilitar mod_rewrite si lo necesitas
RUN a2enmod rewrite

# Configurar Apache para permitir .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Exponer el puerto 80
EXPOSE 80
