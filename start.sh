
#!/bin/bash
set -e

echo "Iniciando MariaDB..."

mkdir -p /run/mysqld /var/lib/mysql
chown -R mysql:mysql /run/mysqld /var/lib/mysql

if [ ! -d /var/lib/mysql/mysql ]; then
    mariadb-install-db --user=mysql --datadir=/var/lib/mysql
fi

mariadbd --user=mysql \
    --datadir=/var/lib/mysql \
    --bind-address=127.0.0.1 \
    --socket=/run/mysqld/mysqld.sock \
    --pid-file=/run/mysqld/mysqld.pid &

echo "Esperando a MariaDB..."

until mariadb-admin --socket=/run/mysqld/mysqld.sock ping --silent; do
    sleep 2
done

echo " iniciado db correctamente."

DB_NAME="${DB_NAME:-autos}"
DB_USER="${DB_USER:-usuario_php}"
DB_PASSWORD="${DB_PASSWORD:-123456}"

mariadb --socket=/run/mysqld/mysqld.sock -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4;"

mariadb --socket=/run/mysqld/mysqld.sock -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"

mariadb --socket=/run/mysqld/mysqld.sock -e "ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"

mariadb --socket=/run/mysqld/mysqld.sock -e "GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';"

if [ -f /database/autos.sql ] && [ ! -f /var/lib/mysql/.autos_imported ]; then
    echo "Importando base de datos..."
    mariadb --socket=/run/mysqld/mysqld.sock "$DB_NAME" < /database/autos.sql
    touch /var/lib/mysql/.autos_imported
fi

echo "Iniciando Apache..."

exec apache2-foreground
