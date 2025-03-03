#!/bin/bash

# Kiểm tra xem /data/options.json có tồn tại không
if [ ! -f /data/options.json ]; then
    echo "Error: /data/options.json not found!" >&2
    exit 1
fi

# Đọc giá trị từ file cấu hình addon
MYSQL_HOST=$(jq --raw-output '.mysql_host' /data/options.json)
MYSQL_USER=$(jq --raw-output '.mysql_user' /data/options.json)
MYSQL_PASSWORD=$(jq --raw-output '.mysql_password' /data/options.json)
MYSQL_DB=$(jq --raw-output '.mysql_db' /data/options.json)

# Thay thế các giá trị trong config.php
sed -i "s|MYSQL_HOST|$MYSQL_HOST|g" /var/www/html/config.php
sed -i "s|MYSQL_USER|$MYSQL_USER|g" /var/www/html/config.php
sed -i "s|MYSQL_PASSWORD|$MYSQL_PASSWORD|g" /var/www/html/config.php
sed -i "s|MYSQL_DB|$MYSQL_DB|g" /var/www/html/config.php

# Cấu hình Apache để hỗ trợ Ingress
#echo "ServerName localhost" >> /etc/apache2/apache2.conf
#a2enmod rewrite
#service apache2 restart

# Khởi động Apache ở chế độ foreground
#exec httpd -DFOREGROUND
exec apache2-foreground
