# DumboPHP #
[![Build Status](https://travis-ci.com/rantes/DumboPHP.svg?branch=master)](https://travis-ci.com/rantes/DumboPHP)
[![Latest Stable Version](https://poser.pugx.org/rantes/dumbophp/v/stable)](https://packagist.org/packages/rantes/dumbophp) [![Total Downloads](https://poser.pugx.org/rantes/dumbophp/downloads)](https://packagist.org/packages/rantes/dumbophp) [![Monthly Downloads](https://poser.pugx.org/rantes/dumbophp/d/monthly)](https://packagist.org/packages/rantes/dumbophp) [![Daily Downloads](https://poser.pugx.org/rantes/dumbophp/d/daily)](https://packagist.org/packages/rantes/dumbophp) [![Latest Unstable Version](https://poser.pugx.org/rantes/dumbophp/v/unstable)](https://packagist.org/packages/rantes/dumbophp) [![License](https://poser.pugx.org/rantes/dumbophp/license)](https://packagist.org/packages/rantes/dumbophp)
![DumboPHP](./logo.png "DumboPHP")
### Summary ###

PHP Framework project built with MVC architecture, OOP paradigm and full ORM (native, not vendor).

### Setup ###

* Get the latest version, clone it or download the zip.
* Unzip if is needed.
* Go to the folder and run the install script:

```
cd /path/to/DumboPHP/
sudo ./install.php
```
#### via composer ####

```
composer require rantes/dumbophp
```

### Server configuration ###

* PHP: Enable short open tags.
* Apache: enable mod_rewrite.

* Consider to set a local domain up with a virtual host).
  - Remember to enable virtual host mod.
  - You can use this config as a sample:
    
```
#!apache

<VirtualHost *:80>
    ServerAdmin webmaster@localhos.com
    ServerName myproject.local
    ServerAlias myproject.local
    DocumentRoot /path/to/myproject
    <Directory /path/to/myproject/>
            Options Indexes FollowSymLinks
            AllowOverride All
            Order allow,deny
            allow from all
    </Directory>

</VirtualHost>
```

```
#!nginx

server {

    root /path/to/myproject/app/webroot;
    index index.php;

    server_name myproject.local;

    set $token "";

    if ($is_args) { # if the request has args update token to "&"
        set $token "&";
    }

    location / {
            set $args "${args}${token}url=${uri}";
            rewrite ^/(.*\.(png|gif|jpg|jpeg|js|pdf|css|ico|svg|json|webp|woff|ttf))$ /$1 break;
            try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/run/php/php7.4-fpm.sock;
    }

    location ~ /\.ht {
            deny all;
    }
}
```

### Go Further ###

For more info, please visite homepage [DumboPHP](http://www.dumbophp.com/).
