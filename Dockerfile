FROM php:8.3-apache-bookworm

RUN a2enmod headers \
    && printf '%s\n' \
       'Options -Indexes' \
       'ServerTokens Prod' \
       'ServerSignature Off' \
       '<Directory /var/www/html>' \
       '    AllowOverride None' \
       '    Require all granted' \
       '</Directory>' \
       'Alias /files/ /srv/isos/' \
       '<Directory /srv/isos>' \
       '    Options -Indexes -FollowSymLinks' \
       '    AllowOverride None' \
       '    Require all granted' \
       '</Directory>' \
       '<FilesMatch "\\.iso$">' \
       '    ForceType application/octet-stream' \
       '    Header set Content-Disposition "attachment"' \
       '</FilesMatch>' \
       'Header always set X-Content-Type-Options "nosniff"' \
       'Header always set Referrer-Policy "strict-origin-when-cross-origin"' \
       > /etc/apache2/conf-available/futura-iso.conf \
    && a2enconf futura-iso

COPY index.php /var/www/html/index.php
COPY styles.css /var/www/html/styles.css
COPY assets/ /var/www/html/assets/

ENV ISO_DIRECTORY=/srv/isos
EXPOSE 80
