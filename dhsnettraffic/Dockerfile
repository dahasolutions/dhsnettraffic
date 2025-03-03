FROM php:7.4-apache

# Cài đặt các extension cần thiết
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN apt-get update && apt-get install -y nano  jq && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite

#RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
#    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf


# Sao chép mã nguồn web vào thư mục gốc của Apache
COPY html /var/www/html/
COPY run.sh /

# Cấu hình quyền và chủ sở hữu
RUN chown -R www-data:www-data /var/www/html
RUN chmod a+x /run.sh

CMD [ "/run.sh" ]

# Expose port 80
EXPOSE 80
