FROM php:8.2-apache

# MariaDB + extensiones de PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
        mariadb-server \
        mariadb-client \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && rm -rf /var/lib/apt/lists/*

# Credenciales por defecto (en Render se pisan con Environment Variables)
ENV DB_NAME=autos \
    DB_USER=usuario_php \
    DB_PASSWORD=cambiame

WORKDIR /var/www/html

# La web
COPY . /var/www/html/

# El respaldo SQL y el script de inicio quedan FUERA de la carpeta pública
RUN mkdir -p /database /run/mysqld /var/lib/mysql \
    && mv /var/www/html/autos.sql /database/autos.sql \
    && mv /var/www/html/start.sh /start.sh \
    && rm -f /var/www/html/Dockerfile /var/www/html/dockerfile \
    && sed -i 's/\r$//' /start.sh \
    && chmod +x /start.sh

EXPOSE 10000
CMD ["/bin/bash", "/start.sh"]
