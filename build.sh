#!/bin/bash

# Functions.
resolve_relative_path() {
    if [ -d "$1" ];then
        cd "$1" || return 1
        pwd
    elif [ -e "$1" ];then
        if [ ! "${1%/*}" = "$1" ]; then
            cd "${1%/*}" || return 1
        fi
        echo "$(pwd)/${1##*/}"
    else
        return 1
    fi
}
__FILE__=$(resolve_relative_path "$0")
__DIR__=$(dirname "$__FILE__")
cd "$__DIR__"

echo php build.php 'result/01-myfolder-as-directory-listing/index.php' --module=user --module=index
php build.php 'result/01-myfolder-as-directory-listing/index.php' --module=user --module=index

echo php build.php 'result/02-myfolder-as-file-manager/index.php' --module=user --module=index --module=markdown
php build.php 'result/02-myfolder-as-file-manager/index.php' --module=user --module=index --module=markdown
