# Базовый образ с nginx и php
FROM richarvey/nginx-php-fpm
ADD . /var/www
RUN rm -Rf /etc/nginx/sites-enabled/*
# Добавляем наш конфиг
ADD docker/nginx/app.conf /etc/nginx/sites-available/app.conf
# Включаем его
RUN ln -s /etc/nginx/sites-available/app.conf /etc/nginx/sites-enabled/app.conf
