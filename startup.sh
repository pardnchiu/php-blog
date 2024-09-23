#!/bin/bash

# 獲取核心數量
cores=$(nproc)

# 獲取記憶體大小（以 MB 為單位）
memory=$(awk '/MemTotal/ {print $2 / 1024}' /proc/meminfo)
memory_consumption=$(echo "$memory / 2" | bc)

# 計算 PHP-FPM 配置值
max_children=$((cores * 32))
start_servers=$((cores * 2))
min_spare_servers=$((cores * 2))
max_spare_servers=$((cores * 16))

# 使用雙引號包裹變量以便於替換
sed -i 's/;*listen.mode/listen.mode/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i "s/;*pm.max_children\s*=\s*[0-9]*/pm.max_children = ${max_children}/" /etc/php/8.2/fpm/pool.d/www.conf
sed -i "s/;*pm.min_spare_servers\s*=\s*[0-9]*/pm.min_spare_servers = ${start_servers}/" /etc/php/8.2/fpm/pool.d/www.conf
sed -i "s/;*pm.start_servers\s*=\s*[0-9]*/pm.start_servers = ${min_spare_servers}/" /etc/php/8.2/fpm/pool.d/www.conf
sed -i "s/;*pm.max_spare_servers\s*=\s*[0-9]*/pm.max_spare_servers = ${max_spare_servers}/" /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/;*pm.max_requests/pm.max_requests/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/;*request_terminate_timeout\s*=\s*[0-9]*[ms]*/request_terminate_timeout = 10s/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/;*rlimit_files/rlimit_files/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/;*rlimit_core/rlimit_core/' /etc/php/8.2/fpm/pool.d/www.conf
echo "opcache.enable=1 \
opcache.memory_consumption=128 \
opcache.interned_strings_buffer=8 \
opcache.max_accelerated_files=4096 \
opcache.revalidate_freq=60 \
opcache.validate_timestamps=1 \
opcache.max_wasted_percentage=5 \
opcache.fast_shutdown=1 \
opcache.enable_cli=1" >> /etc/php/8.2/cli/conf.d/10-opcache.ini
echo "pdo_mysql.cache_size = 4096 \
pdo_mysql.default_socket=/run/mysqld/mysqld.sock" >> /etc/php/8.2/cli//conf.d/10-pdo.ini

# 使用 exec 啟動進程，並且保持前台運行
service php8.2-fpm start
apache2ctl -D FOREGROUND