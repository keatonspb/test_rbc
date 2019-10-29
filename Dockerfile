# Базовый образ с nginx и php
FROM richarvey/nginx-php-fpm
ADD . /var/www
RUN rm -Rf /etc/nginx/sites-enabled/*
# Добавляем наш конфиг
ADD docker/nginx/app.conf /etc/nginx/sites-available/app.conf
# Включаем его
RUN ln -s /etc/nginx/sites-available/app.conf /etc/nginx/sites-enabled/app.conf


#Настраиваем cron
COPY docker/crontab /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab
RUN crontab /etc/cron.d/crontab
RUN touch /var/log/cron.log

RUN crond start

