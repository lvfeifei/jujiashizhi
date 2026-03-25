FROM rockylinux:8

# 1. 设置时区
ENV TZ=Asia/Shanghai
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# 2. 初始化：替换 Rocky 官方源为国内源 (加速基础下载)
# 同时清理缓存，确保使用新配置
RUN sed -i 's/mirror.rockylinux.org/mirrors.aliyun.com/g' /etc/yum.repos.d/Rocky-*.repo && \
    dnf clean all && \
    dnf makecache

# 3. 安装 epel-release 和 remi-release
# 注意：这里先安装 rpm 包，此时源可能还不通，但 rpm 包很小通常能成功
RUN dnf install -y epel-release && \
    dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm

# 4. 【关键步骤】强制替换 EPEL 源为阿里云固定地址
# 默认的 mirrorlist 经常失效，我们直接写入 baseurl
RUN echo "[epel]" > /etc/yum.repos.d/epel.repo && \
    echo "name=Extra Packages for Enterprise Linux 8 - x86_64" >> /etc/yum.repos.d/epel.repo && \
    echo "baseurl=https://mirrors.aliyun.com/epel/8/Everything/x86_64/" >> /etc/yum.repos.d/epel.repo && \
    echo "enabled=1" >> /etc/yum.repos.d/epel.repo && \
    echo "gpgcheck=1" >> /etc/yum.repos.d/epel.repo && \
    echo "gpgkey=https://mirrors.aliyun.com/epel/RPM-GPG-KEY-EPEL-8" >> /etc/yum.repos.d/epel.repo && \
    echo "exclude=^php.*" >> /etc/yum.repos.d/epel.repo && \
    dnf clean all && \
    dnf makecache

# 5. 启用 PHP 7.4 模块 (Remi 源)
RUN dnf module reset -y php && \
    dnf module enable -y php:remi-7.4 && \
    dnf clean all && \
    dnf makecache

# 6. 安装 PHP 7.4 及扩展
# 修正了部分包名，确保与 Remi 源匹配
RUN dnf install -y \
    php74-php-fpm \
    php74-php-gd \
    php74-php-mysqlnd \
    php74-php-pdo \
    php74-php-mbstring \
    php74-php-xml \
    php74-php-pecl-memcached \
    php74-php-curl \
    php74-php-json \
    php74-php-pecl-zip \
    php74-php-cli \
    php74-php-opcache \
    php74-php-bcmath \
    php74-php-pecl-redis \
    php74-php-process \
    && dnf clean all

# 6.1
RUN dnf install -y \
    ImageMagick ImageMagick-devel \
    php74-php-pecl-imagick

# 7. 配置 PHP-FPM (改为 TCP 监听 9000)
RUN sed -i 's/listen = \/var\/opt\/remi\/php74\/run\/php-fpm\/www.sock/listen = 0.0.0.0:9000/' /etc/opt/remi/php74/php-fpm.d/www.conf && \
    sed -i 's/listen.allowed_clients = 127.0.0.1/; listen.allowed_clients = 127.0.0.1/' /etc/opt/remi/php74/php-fpm.d/www.conf

# 8. 建立软链接
RUN if [ -f /usr/bin/php74 ]; then \
        rm -f /usr/bin/php && \
        ln -s /usr/bin/php74 /usr/bin/php; \
    fi

# 9. 验证
RUN php -v

EXPOSE 9000

# 10. 启动
RUN ln -s /opt/remi/php74/root/usr/sbin/php-fpm /usr/bin/php-fpm
CMD ["php-fpm", "-F"]
