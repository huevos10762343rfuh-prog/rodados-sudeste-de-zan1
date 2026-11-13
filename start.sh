#!/bin/bash
set -e

SOCK=/run/mysqld/mysqld.sock
DB_NAME="${DB_NAME:-autos}"
DB_USER="${DB_USER:-usuario_php}"
DB_PASSWORD="${DB_PASSWORD:-cambiame}"
PORT="${PORT:-10000}"

# Apache escucha en el puerto que pida Render ($PORT, por defecto 10000)
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

echo "Iniciando db..."
mkdir -p /run/mysqld /var/lib/mysql
chown -R mysql:mysql /run/mysqld /var/lib/mysql

if [ ! -d /var/lib/mysql/mysql ]; then
    mariadb-install-db --user=mysql --datadir=/var/lib/mysql
fi

mariadbd --user=mysql \
    --datadir=/var/lib/mysql \
    --bind-address=127.0.0.1 \
    --socket="$SOCK" \
    --pid-file=/run/mysqld/mysqld.pid &

echo "Esperando a MariaDB..."
until mariadb-admin --socket="$SOCK" ping --silent; do
    sleep 1
done
echo "db listo."

mariadb --socket="$SOCK" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4;"
mariadb --socket="$SOCK" -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
mariadb --socket="$SOCK" -e "ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
mariadb --socket="$SOCK" -e "GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;"

# Importa el respaldo una sola vez
if [ -f /database/autos.sql ] && [ ! -f /var/lib/mysql/.autos_imported ]; then
    echo "Importando autos.sql..."
    mariadb --socket="$SOCK" "$DB_NAME" < /database/autos.sql
    touch /var/lib/mysql/.autos_imported
fi

echo "Iniciando Apache en el puerto ${PORT}..."
exec apache2-foreground
