# Gitpod docker image for WordPress | https://github.com/luizbills/gitpod-wordpress
# License: MIT (c) 2019 Luiz Paulo "Bills"
# Version: 0.5
FROM gitpod/workspace-mysql

### General Settings ###
ENV PHP_VERSION="7.4"
ENV APACHE_DOCROOT="public_html"

### Setups, Node, NPM ###
USER gitpod
ADD https://api.wordpress.org/secret-key/1.1/salt /dev/null
RUN git clone https://github.com/luizbills/gitpod-wordpress $HOME/gitpod-wordpress && \
    cat $HOME/gitpod-wordpress/conf/.bashrc.sh >> $HOME/.bashrc && \
    . $HOME/.bashrc && \
    bash -c ". .nvm/nvm.sh && nvm install --lts"

### MailHog ###
USER root
RUN go get github.com/mailhog/MailHog && \
    go get github.com/mailhog/mhsendmail && \
    cp $GOPATH/bin/MailHog /usr/local/bin/mailhog && \
    cp $GOPATH/bin/mhsendmail /usr/local/bin/mhsendmail && \
    ln $GOPATH/bin/mhsendmail /usr/sbin/sendmail && \
    ln $GOPATH/bin/mhsendmail /usr/bin/mail &&\
    ### Apache ###
    apt-get -y install apache2 && \
    chown -R gitpod:gitpod /var/run/apache2 /var/lock/apache2 /var/log/apache2 && \
    echo "include ${HOME}/gitpod-wordpress/conf/apache.conf" > /etc/apache2/apache2.conf && \
    echo ". ${HOME}/gitpod-wordpress/conf/apache.env.sh" > /etc/apache2/envvars && \
    ### PHP ###
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get -y install \
        libapache2-mod-php \
        php${PHP_VERSION} \
        php${PHP_VERSION}-common \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-intl \
        php${PHP_VERSION}-mysql \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-json \
        php${PHP_VERSION}-zip \
        php${PHP_VERSION}-soap \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-opcache \
        php-xdebug && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* && \
    cat /home/gitpod/gitpod-wordpress/conf/php.ini >> /etc/php/${PHP_VERSION}/apache2/php.ini && \
    a2dismod php* && \
    a2dismod mpm_* && \
    a2enmod mpm_prefork && \
    a2enmod php${PHP_VERSION} && \
    ### WP-CLI ###
    wget -q https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -O $HOME/wp-cli.phar && \
    wget -q https://raw.githubusercontent.com/wp-cli/wp-cli/v2.3.0/utils/wp-completion.bash -O $HOME/wp-cli-completion.bash && \
    chmod +x $HOME/wp-cli.phar && \
    mv $HOME/wp-cli.phar /usr/local/bin/wp && \
    chown gitpod:gitpod /usr/local/bin/wp

### WordPress, Adminer ###
USER gitpod
ADD https://api.wordpress.org/secret-key/1.1/salt /dev/null
RUN wget -q https://wordpress.org/latest.zip -O $HOME/wordpress.zip && \
    unzip -qn $HOME/wordpress.zip -d $HOME && \
    unlink $HOME/wordpress.zip && \
    cp $HOME/gitpod-wordpress/conf/.htaccess $HOME/wordpress/.htaccess && \
    mkdir $HOME/wordpress/database/ && \
    wget -q https://www.adminer.org/latest.php -O $HOME/wordpress/database/index.php && \
    mkdir $HOME/wordpress/phpinfo/ && \
    echo "<?php phpinfo(); ?>" > $HOME/wordpress/phpinfo/index.php