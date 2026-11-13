
FROM php:8.2-apache

# Instalar MariaDB y herramientas
RUN apt-get update && apt-get install -y \
    mariadb-server \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && rm -rf /var/lib/apt/lists/*

# Configurar Apache para Render
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:10000>/' /etc/apache2/sites-available/000-default.conf

# Preparar carpetas
RUN mkdir -p /var/lib/mysql /run/mysqld

WORKDIR /var/www/html

COPY . /var/www/html/

# Sacar el respaldo SQL de la carpeta pública
RUN mkdir -p /database \
    && if [ -f /var/www/html/autos.sql ]; then \
       mv /var/www/html/autos.sql /database/autos.sql; \
       fi

# Crear el inicio automático de PHP y MySQL
RUN cat > /start.sh <<'EOF'
#!/bin/bash
set -e

mkdir -p /run/mysqld /var/lib/mysql
chown -R mysql:mysql /run/mysqld /var/lib/mysql

# Inicializar MySQL si es la primera ejecución
if [ ! -d /var/lib/mysql/mysql ]; then
    mariadb-install-db --user=mysql --datadir=/var/lib/mysql
fi

# Iniciar MariaDB
mariadbd --user=mysql \
    --datadir=/var/lib/mysql \
    --bind-address=127.0.0.1 \
    --socket=/run/mysqld/mysqld.sock \
    --pid-file=/run/mysqld/mysqld.pid &

# Esperar a MySQL
until mariadb-admin ping --silent; do
    sleep 2
done

# Crear base y usuario
DB_NAME="${DB_NAME:-autos}"
DB_USER="${DB_USER:-root}"
DB_PASSWORD="${DB_PASSWORD:-123456}"

mariadb -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4;"

if [ "$DB_USER" != "root" ]; then
    mariadb -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
    mariadb -e "ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
    mariadb -e "GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';"
fi

# Importar el respaldo una sola vez
if [ -f /database/autos.sql ] && [ ! -f /var/lib/mysql/.autos_imported ]; then
    mariadb "$DB_NAME" < /database/autos.sql
    touch /var/lib/mysql/.autos_imported
fi

# Iniciar Apache
exec apache2-foreground
EOF

RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/bin/bash", "/start.sh"]
