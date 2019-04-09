# https://serverfault.com/questions/302592/include-files-in-apache-http-server-version-2-2-httpd-conf

FROM centos/httpd-24-centos7

USER root

# Copy apache vhost files to proxy php requests to php-fpm container
COPY httpd-local.conf /etc/httpd/conf.d/httpd-local.conf
COPY httpd-ssl.conf /etc/httpd/conf.d/httpd-ssl.conf

# RUN yum update && yum install -y
#RUN yum -y update && yum clean all
RUN yum install nano -y
#RUN yum install htop -y
#RUN yum install openssl -y

RUN mkdir /etc/ssl/private

# Create SSL certificates
# IMPORTANT:
#   server.key and server.crt must match what is in httpd-ssl.conf
#   Set CN to localhost (will work with other localhost domains in hosts file)
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/server.key -out /etc/ssl/certs/server.crt -subj "/C=AU/ST=NSW/L=Sydney/O=Website/OU=Development/CN=localhost"

# Modify httpd.conf
#RUN sed -i \
#  -e 's/^#\(Include .*httpd-ssl.conf\)/\1/' \
#  -e 's/^#\(LoadModule .*mod_ssl.so\)/\1/' \
#  -e 's/^#\(LoadModule .*mod_socache_shmcb.so\)/\1/' \
#  conf/httpd.conf

# Also listen on port 80.
# Apache is set to default to port 8080.
RUN echo "Listen 0.0.0.0:80" \
    >> /etc/httpd/conf/httpd.conf

RUN echo "Listen 0.0.0.0:443" \
    >> /etc/httpd/conf.d/ssl.conf

# NOT NEEDED. FILES IN /conf.d ALREADY SET TO BE INCLUDED
# Add reference to httpd-local.conf into httpd.conf
#RUN echo "Include /etc/httpd/conf/httpd-local.conf" \
#    >> /etc/httpd/conf/httpd.conf