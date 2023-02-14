#!/bin/bash

# Common function.
red() { echo -ne "\e[91m"; echo -n "$@"; echo -e "\e[39m"; }
green() { echo -ne "\e[92m"; echo -n "$@"; echo -e "\e[39m"; }
yellow() { echo -ne "\e[93m"; echo -n "$@"; echo -e "\e[39m"; }
blue() { echo -ne "\e[94m"; echo -n "$@"; echo -e "\e[39m"; }
magenta() { echo -ne "\e[95m"; echo -n "$@"; echo -e "\e[39m"; }
x() { exit 1; }
e() { echo "$@"; }
_() { echo -n "$@"; }
__() { echo -n '    '; [ -n "$1" ] && echo "$@" || echo -n ; }
____() { echo; }

# Variable.
domain='myfolder.my.id'
database='/var/www/project/'$domain'/.htpasswd'
touch $database

# Nginx
yellow Mencari informasi nginx.
conf_path=$(nginx -V 2>&1 | grep -o -P -- '--conf-path=\K(\S+)')
magenta conf_path="$conf_path"
user_nginx=$(cat "$conf_path" | grep -o -P 'user\s+\K([^;]+);' | sed 's/;//')
magenta user_nginx="$user_nginx"
if [ $(stat $database -c %U) == $user_nginx ];then
    __ File '`'$database'`' dimiliki oleh user '`'$user_nginx'`'.
else
    __ File '`'$database'`' tidak dimiliki oleh user '`'$user_nginx'`'.
    tweak=1
fi
if [ -n "$tweak" ];then
    chown -R $user_nginx:$user_nginx $database
    if [ $(stat --cached=never $database -c %U) == $user_nginx ];then
        __; green File '`'$database'`' dimiliki oleh user '`'$user_nginx'`'.
    else
        __; red File '`'$database'`' tidak dimiliki oleh user '`'$user_nginx'`'.; x
    fi
fi
yellow Menambah user ke database.

# Argument.
_username="$1"
if [ -z "$_username" ];then
    read -p "<username>: " _username
fi
[ -n "$_username" ] || { red "Argument <username> required."; x; }
[[ $_username = *" "* ]] && { red "Argument <username> can not contain space."; x; }
[ -n "$_username" ] && {
    if ! grep -q -E '^[a-z][a-z0-9]+' <<< "$_username";then
        red "Argument <username> contains invalid characters."; x
    fi
    if grep -q "^$_username\:" "$database";then
        red "Username $_username is exists."; x
    fi
}
_password="$2"
if [ -z "$_password" ];then
    read -p "<password>: " _password
fi
[ -n "$_password" ] || { red "Argument <password> required."; x; }

# Populate value.
username=$_username
password=$_password

# Execute.
mkdir -p $(dirname $database)
echo "$password" | htpasswd -i "$database" "$username"
