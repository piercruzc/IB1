# Imagen base de PHP con Apache
FROM php:8.1-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql fileinfo

# Aumentar límites de subida de PHP
RUN echo "upload_max_filesize = 10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 12M" >> /usr/local/etc/php/conf.d/uploads.ini

# Copiar el código de tu aplicación
COPY . /var/www/html/

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar Apache para permitir .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Exponer el puerto 80
EXPOSE 80

# Fijar permisos de uploads en cada inicio y arrancar Apache
CMD mkdir -p /var/www/html/uploads/blog \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads \
    && apache2-foreground
