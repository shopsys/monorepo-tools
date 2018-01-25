FROM phpdockerio/php72-fpm:latest

# Switch to non-interactive mode during image build to silence some warnings.
# See https://github.com/moby/moby/issues/4032
ARG DEBIAN_FRONTEND=noninteractive

# Install packages are useful when working in the container in interactive mode.
RUN apt-get update && apt-get -y install apt-utils dialog

# Install ACL support (ie. tools like "setfacl").
RUN apt-get update && apt-get -y install acl

# Install Git - it is necessary for code diffs
RUN apt-get update && apt-get -y install git

# Install locales and switch to en_US.utf8 in order to enable UTF-8 support.
# See https://perlgeek.de/en/article/set-up-a-clean-utf8-environment
RUN apt-get update && apt-get -y install locales-all
ENV LC_ALL=en_US.utf8 LANG=en_US.utf8 LANGUAGE=en_US.utf8

# Install required PHP extensions
RUN apt-get update && apt-get -y --no-install-recommends install \
        php7.2-bcmath \
        php7.2-curl \
        php7.2-fpm \
        php7.2-gd \
        php7.2-intl \
        php7.2-json \
        php7.2-mbstring \
        php7.2-opcache \
        php7.2-pgsql \
        php7.2-soap \
        php7.2-xml \
        php7.2-zip

# Install Composer
RUN php -r "copy('https://getcomposer.org/download/1.5.1/composer.phar', '/usr/local/bin/composer');" \
    && php -r "if (hash_file('SHA384', '/usr/local/bin/composer') === 'fd3800adeff12dde28e9238d2bb82ba6f887bc6d718eee3e3a5d4f70685a236b9e96afd01aeb0dbab8ae6211caeb1cbe') {echo 'Composer installed';} else {echo 'Hash invalid for downloaded composer.phar'; exit(1);}" \
    && chmod 0755 /usr/local/bin/composer \
    && /usr/local/bin/composer selfupdate --stable

# Install Node.js v6 from NodeSource repository that also contains NPM.
# See https://nodejs.org/en/download/package-manager/#debian-and-ubuntu-based-linux-distributions
RUN curl -sL https://deb.nodesource.com/setup_6.x | bash - \
    && apt-get update && apt-get -y install nodejs=6.*

WORKDIR "/var/www/shopsys-framework"
