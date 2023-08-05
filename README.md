# MyFolder

MyFolder is Single file PHP for directory listing.

This project aims to be elegant version of directory listing by apache or nginx.

This project can be extended into your file manager, even into your personal cloud storage.

## State

Under active development.

## Demo

http://myfolder.my.id/

## Installation

Just put file `index.php` in your public directory, then access it via browser.

Sample config if you using Nginx for rewrite rules :

```
server {
    listen 80;
    listen [::]:80;
    root /var/www/myfolder.my.id/web;
    index index.php;
    server_name myfolder.my.id;
    location / {
        try_files $uri /index.php$is_args$args;
    }
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }
}
```

Put `.htaccess` file in same folder of `index.php` if you using Apache as webserver for rewrite rules.

## Todo
- [x] OOP Style.
- [ ] Password for user hacker.
- [ ] Password for user admin.
- [ ] Modular (plug-in system).
- [ ] Ability to enable/disable module .
- [ ] Ability to change `$target_directory` by admin.
- [ ] Feature multi user (by module).
- [ ] Feature multi storage per user (by module).
- [ ] Feature file operations (by module).
- [ ] Dual display.
- [ ] Separate file to configuration.
- [ ] Toggle to switch offline or online mode for resources.

## MyFolder vs TinyFileManager

| Feature         | MyFolder        | TinyFileManager |
|-----------------|-----------------|-----------------|
| File Operations | yes (by module) | yes             |
| PHP Version     | 5.3             | 5.5             |
| Multi User      | yes (by module) | yes             |
| Extends         | yes             | no              |
| MultiLingual    | no              | yes             |
