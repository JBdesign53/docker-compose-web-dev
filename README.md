# Local Web Development Environment

Deploy a web development environment for multiple domains running on different versions of PHP.

* Suitable as a local web development environment.
* Run multiple websites and browse to them by domain name.
* Access each website over HTTP and HTTPS. (SSL certificates are created during the build process.)
* Each website can use a different version of PHP.
* The stack uses CentOS, Apache and PHP-FPM.


## Initial Setup

Install Docker for your operating system. For more information visit https://docs.docker.com/install/

After cloning this repository there are a few setup steps to complete before confirming the initial configuration is working.

Copy the contents from `/extra/hosts` and paste it at the end of your local `hosts` file and save.

The location of your hosts file varies depending on your operating system:

* Windows: `C:/Windows/System32/drivers/etc/hosts`
* Mac/Linux: `/etc/hosts`

Next, from your command line:
* Navigate to the `docker-compose-web-dev` folder.
* Run `docker-compose up --build`.
* Once the build finishes, open your browser and navigate to: http://loc.php73.com
* You should see a page titled *HTML Test Page for /php73*.

The following domains should now be reachable through your web browser:

PHP 5.6:
* http://loc.php56.com
* https://loc.php56.com

PHP 7.0:
* http://loc.php70.com
* https://loc.php70.com

PHP 7.3:
* http://loc.php73.com
* https://loc.php73.com


## Running the Environment

Navigate to the `docker-compose-web-dev` folder.

Start the environment with:
`docker-compose up --build`

Stop the environment with:
`ctrl-c` and then `docker-compose down`


## Serving Your Own Website Files

Note: It is recommended that you follow the steps in **Initial Setup** to confirm the local web development environment is working before making changes to serve your own web files.

Additional configuration is required to setup local domain names and to serve your web files. After reading this section refer to **Example Website Setup** for detailed information.

Complete one-time configuration:
* Provide the path to your website directory. This will contain each website/domain in its own child directory.

Then, for each new website/domain create an additional:
* Child directory in the website directory. Copy the files for your website/domain into the child directory.
* Hosts file entry. This forwards the website domain name to Apache.
* Vhosts entry. This tells Apache where your website files are located and which PHP version to use.

**Note:** Alternatively, you can copy your website files to the `/docker/www/` directory. In this case, you only need to add new hosts and vhost entries.


## Example Website Setup

This section shows an example of how to create a new local website at the domain `loc.my-website.com`.

In this example:
  * Web files are located at `C:/www/my-website`.
  * The website can be browsed to at `http://loc.my-website.com` or `https://loc.my-website.com`.
  * PHP 7.3 is used.

To achieve this three files will be modified inside the repository:
  * env
  * httpd-local.conf
  * httpd-ssl.conf
  
File location:

```
docker-compose-web-dev
├── env
└── docker/
    └── apache/
        ├── httpd-local.conf
        └── httpd-ssl.conf
```


### 1. Set the Website Directory Path

The **env** file is set to:

```
HTML_VOLUME=C:/www
```


### 2. Create Website Files

The website files are copied to the directory `C:/www/my-website`.


### 3. Update the Hosts File

The following entry is added to the hosts file: 

```
127.0.0.1  loc.my-website.com
:1  loc.my-website.com
```


### 4. Configure HTTP Settings

The **httpd-local.conf** file is updated with a VirtualHost entry which reads:

``` apache
<VirtualHost *:80>
  ServerName my-website.com
  ServerAlias www.my-website.com *.my-website.com

  DocumentRoot /var/www/html/my-website
  
  <Directory /var/www/html/my-website>
    DirectoryIndex index.html index.php
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>

  ProxyPassMatch ^/(.*\.php(/.*)?)$ fcgi://php73:9000/var/www/html/my-website/$1
  
  CustomLog /proc/self/fd/1 common
  ErrorLog /proc/self/fd/2
</VirtualHost>
```

Note where `my-website` appears in the code.

Pay particular attention to the line `ProxyPassMatch`. Make sure to set `fcgi://php73` to the version of PHP you want to use.


### 5. Configure HTTPS Settings

The **httpd-ssl.conf** file is updated with a VirtualHost entry which reads:

``` apache
<VirtualHost *:443>
  ServerName my-website.com
  ServerAlias www.my-website.com *.my-website.com

  DocumentRoot /var/www/html/my-website

  <Directory /var/www/html/my-website>
    DirectoryIndex index.html index.php
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>

  ProxyPassMatch ^/(.*\.php(/.*)?)$ fcgi://php73:9000/var/www/html/my-website/$1

  CustomLog /proc/self/fd/1 common
  ErrorLog /proc/self/fd/2

  SSLEngine on

  SSLCertificateFile "/etc/ssl/certs/server.crt"
  SSLCertificateKeyFile "/etc/ssl/private/server.key"
</VirtualHost>
```

Note where `my-website` appears in the code.

Pay particular attention to the line `ProxyPassMatch`. Make sure to set `fcgi://php73` to the version of PHP you want to use.


### 6. Build and View the Website

Run the command `docker-compose up --build`. This is needed to force the build to refresh since changes have been made to the Docker configuration files.

The website can now be reached from a web browser at:
  * `http://loc.my-website.com`
  * `https://loc.my-website.com`


## Website Directory Path

Using the default setup, Docker serves your web files from the `/docker/www/` directory. Each domain/website is in its own directory within this folder.

To change the directory location open the `/env` file and set a new `HTML_VOLUME` directory path. This should point to the dirctory containing all your website files.

Each domain/website should be in its own directory similar to the directory structure in `/docker/www/`.


## Technical information

### Website Files
Website files are mounted from the local file system at ./www into CentOS at /var/www/html/.

To show different website files modify the `HTML_VOLUME` variable in the ./.env file to point to your local website files.

### What Gets Built
Docker Compose builds several containers. There are also additional setup steps to help with web development, such as adding SSL and Xdebug support.

Docker containers:
* CentOS 7 with Apache 2.4
* PHP-FPM 5.6, 7.0, 7.3
* MariaDB - Disabled by default. To enable, uncomment lines in the /docker-compose.yml file.

Additional setup steps and installation:
* SSL keys using OpenSSL
* Xdebug 2.5.5
* Nano

### Ports
The following ports are exposed by Apache and Docker Compose:

* 80 (HTTP)
* 443 (HTTPS)

## Security Risks

There are security implications for how self-signed SSL certificates and MariaDB (root access) are setup which make this deployment unsuitable for production environments.
