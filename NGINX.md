# Introduction #

Converted .htaccess to nginx rewrite format


# Details #
From (.htaccess):

```
RewriteEngine on
RewriteRule ^(.+)/?$ index.php [L]
```

To (/etc/nginx/sites-available/default):
```
location /restbed {
        rewrite ^/(.+)/?$ /restbed/index.php last;
    }
```