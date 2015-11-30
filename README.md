# Factor-Checker
A simple website that creates a dedicated page for check indivudual facotrs based on [eRanker.com](https://www.eRanker.com)  API.

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