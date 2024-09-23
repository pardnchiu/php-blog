FROM --platform=linux/amd64 debian:stable

# 安装package
RUN apt update && \
    apt install -y \
    tzdata \
    nano \
    ca-certificates \
    curl \
    gnupg \
    bc \
    apache2 \
    php \
    php-fpm \
    php-dev \
    php-cli \
    php-mysql \
    php-bcmath \
    php-json \
    php-mbstring \
    php8.2-common \
    php-tokenizer \
    php-xml \
    php-pear \
    php-zip \
    php-curl \
    php-redis \
    php-gd \
    php-imagick \
    php-opcache \
    composer \
    libapache2-mod-fcgid

# 設置時區
RUN ln -snf /usr/share/zoneinfo/"Asia/Taipei" /etc/localtime && \
    echo "Asia/Taipei" > /etc/timezone && \
    dpkg-reconfigure -f noninteractive tzdata

# 設置權限
RUN groupadd webadmin && \
    usermod -a -G webadmin www-data && \
    usermod -a -G webadmin root && \
    chown -R root:webadmin /var/www && \
    chmod g+s /var/www && \
    find /var/www -type d -exec chmod g+s {} + && \
    find /var/www -type d -exec chmod 775 {} + && \
    find /var/www -type f -exec chmod 664 {} + && \
    echo 'umask 002' >> ~/.profile && \
    echo 'umask 002' >> ~/.bashrc

# Apache 配置
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    echo "<IfModule mpm_prefork_module>\nStartServers 2\nMinSpareServers 2\nMaxSpareServers 4\nServerLimit 32\nMaxClients 32\nMaxRequestsPerChild 512\n</IfModule>" >> /etc/apache2/apache2.conf && \
    sed -i 's|DocumentRoot \/var\/www\/html|DocumentRoot \/var\/www\/html\n<Directory \/var\/www\/html>\nOptions Indexes FollowSymLinks\nAllowOverride All\nRequire all granted\n<\/Directory>\n<FilesMatch \\.php\$>\nSetHandler \"proxy\:unix\:\/run\/php\/php8\.2\-fpm\.sock\|fcgi\:\/\/localhost\"\n<\/FilesMatch>|g' /etc/apache2/sites-available/000-default.conf

# PHP-FPM 配置
COPY ./startup.sh /usr/local/bin/startup.sh

# 列出 /usr/local/bin 的內容以確認複製是否成功
RUN ls -l /usr/local/bin/

# PHP 配置
RUN chmod +x /usr/local/bin/startup.sh

# 启用和禁用Apache模块
RUN a2dismod php8.2 mpm_prefork && \
    a2enmod rewrite proxy_fcgi setenvif mpm_event && \
    a2enconf php8.2-fpm && \
    a2ensite 000-default

EXPOSE 80

CMD ["/usr/local/bin/startup.sh"]