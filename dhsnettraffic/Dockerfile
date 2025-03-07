FROM php:7.4-apache

# Cài đặt các extension cần thiết
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN apt-get update && apt-get install -y nano jq && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite

# Thay thế ErrorLog nếu đã tồn tại, nếu không thì thêm vào cuối file
RUN grep -q "^ErrorLog" /etc/apache2/apache2.conf && \
    sed -i 's|^ErrorLog .*|ErrorLog /dev/null|g' /etc/apache2/apache2.conf || \
    echo "ErrorLog /dev/null" >> /etc/apache2/apache2.conf

# Thay thế CustomLog nếu đã tồn tại, nếu không thì thêm vào cuối file
RUN grep -q "^CustomLog" /etc/apache2/apache2.conf && \
    sed -i 's|^CustomLog .*|CustomLog /dev/null combined|g' /etc/apache2/apache2.conf || \
    echo "CustomLog /dev/null combined" >> /etc/apache2/apache2.conf

# Sao chép mã nguồn web vào thư mục gốc của Apache
COPY html /var/www/html/
COPY run.sh /

# Cấu hình quyền và chủ sở hữu
RUN chown -R www-data:www-data /var/www/html
RUN chmod a+x /run.sh

CMD [ "/run.sh" ]

# Expose port 80
EXPOSE 80
