# eRanker Factor Checker Website
A simple website that creates a dedicated page for check indivudual factors based on [eRanker.com](https://www.eRanker.com)  API.
You can install on any folder of your website and do the redirects using a .htaccess file.

> You will need a eRanker.com API Key in order to run the projet in your server. You can modify this code the way you want, adding a logo, pdf headers and urls.

## If you want to develop, you can use the virtual host below as example:
```
<VirtualHost 127.0.0.1>
    DocumentRoot "D:\GEORANKER\factor-checker-website"
    ServerName 127.0.0.1
    <Directory "D:\GEORANKER\factor-checker-website">
            Options FollowSymLinks Indexes
            AllowOverride All
            Order deny,allow
            Allow from 127.0.0.1
            Deny from all
            Require all granted
    </Directory>
</VirtualHost>
```