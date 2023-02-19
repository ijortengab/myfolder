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

__FILE__=$(realpath "$0")
__DIR__=$(dirname "$__FILE__")
[ -f "${__DIR__}/config.php" ] || { red Config file '`'config.php'`' is not found.; x; }
[ -f "${__DIR__}/nginx.tpl.conf" ] || { red Template file '`'nginx.tpl.conf'`' is not found.; x; }

php=$(cat <<-'EOF'
$file = $_SERVER['argv'][1];
include($file);
$variable = $_SERVER['argv'][2];
echo rtrim($$variable, '/');
EOF
)

domain=$(php -r "$php" "${__DIR__}/config.php" domain)
installation_directory=$(php -r "$php" "${__DIR__}/config.php" installation_directory)
user_storage_directory=$(php -r "$php" "${__DIR__}/config.php" user_storage_directory)
public_storage_directory=$(php -r "$php" "${__DIR__}/config.php" public_storage_directory)

domain_quoted=$(sed "s/\./\\\./g" <<< "$domain")
domain_quoted=$(sed "s/\./\\\./g" <<< "$domain_quoted")
sed -e 's|INSTALLATION_DIRECTORY|'"$installation_directory"'|g' \
    -e 's|USER_STORAGE_DIRECTORY|'"$user_storage_directory"'|g' \
    -e 's|PUBLIC_STORAGE_DIRECTORY|'"$public_storage_directory"'|g' \
    -e 's|DOMAIN_QUOTED|'"$domain_quoted"'|g' \
    -e 's|DOMAIN|'"$domain"'|g' \
    "${__DIR__}/nginx.tpl.conf"
