FROM uspdev/uspdev-php-apache:latest

# apache
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# composer
USER www-data
COPY --chown=www-data . .
RUN composer install

CMD ["apache2-foreground"]
