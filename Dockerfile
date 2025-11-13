# Imagen base de PHP con Apache
FROM php:8.1-apache

# Copiar el código de tu aplicación
COPY . /var/www/html/

# Habilitar mod_rewrite si lo necesitas
RUN a2enmod rewrite

# Exponer el puerto 80
EXPOSE 80
