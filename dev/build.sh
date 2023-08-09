#!/bin/bash

# Common Functions.
red() { echo -ne "\e[91m" >&2; echo -n "$@" >&2; echo -ne "\e[39m" >&2; }
green() { echo -ne "\e[92m" >&2; echo -n "$@" >&2; echo -ne "\e[39m" >&2; }
yellow() { echo -ne "\e[93m" >&2; echo -n "$@" >&2; echo -ne "\e[39m" >&2; }
blue() { echo -ne "\e[94m" >&2; echo -n "$@" >&2; echo -ne "\e[39m" >&2; }
magenta() { echo -ne "\e[95m" >&2; echo -n "$@" >&2; echo -ne "\e[39m" >&2; }
error() { echo -n "$INDENT" >&2; red "$@" >&2; echo >&2; }
success() { echo -n "$INDENT" >&2; green "$@" >&2; echo >&2; }
chapter() { echo -n "$INDENT" >&2; yellow "$@" >&2; echo >&2; }
title() { echo -n "$INDENT" >&2; blue "$@" >&2; echo >&2; }
code() { echo -n "$INDENT" >&2; magenta "$@" >&2; echo >&2; }
x() { echo >&2; exit 1; }
e() { echo -n "$INDENT" >&2; echo "$@" >&2; }
_() { echo -n "$INDENT" >&2; echo -n "$@" >&2; }
_,() { echo -n "$@" >&2; }
_.() { echo >&2; }
__() { echo -n "$INDENT" >&2; echo -n '    ' >&2; [ -n "$1" ] && echo "$@" >&2 || echo -n  >&2; }
____() { echo >&2; [ -n "$delay" ] && sleep "$delay"; }

# Functions.
fileMustExists() {
    # global used:
    # global modified:
    # function used: __, success, error, x
    if [ -f "$1" ];then
        __; green File '`'$(basename "$1")'`' ditemukan.; _.
    else
        __; red File '`'$(basename "$1")'`' tidak ditemukan.; x
    fi
}
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

chapter Change Directory
code cd \""$__DIR__"\"
cd "$__DIR__"
____

chapter Memeriksa File.
fileMustExists ./index.php
fileMustExists ./index.html
fileMustExists ./script.js
____

file=./index.php
chapter Menghapus credentials.
code sed -i \''/^$/d'\' \""$file"\"
code sed -i \''s,^\.sysadmin\.name.*,.sysadmin.name,'\' \""$file"\"
code sed -i \''s,^\.sysadmin\.pass.*,.sysadmin.pass,'\' \""$file"\"
sed -i 's,^\.sysadmin\.name.*,.sysadmin.name,' "$file"
sed -i 's,^\.sysadmin\.pass.*,.sysadmin.pass,' "$file"
____

chapter Menimpa dengan file dari development.
code cp ./index.php ../index.php
cp ./index.php ../index.php
____

source=index.html
chapter Menggabungkan File '`'$source'`'.
file=../index.php
string="return file_get_contents(__DIR__.'/$source');"
number_1=$(grep -n -F "$string" "$file" | head -1 | cut -d: -f1)
e Mengganti baris '`'"$string"'`'
number_1plus=$((number_1 - 1))
number_1plus2=$((number_1 + 1))
part1=$(sed -n '1,'$number_1plus'p' "$file")
part2=$(sed -n $number_1plus2',$p' "$file")
opening="        return <<<'EOF'"
closing="EOF;"
additional=$(<"$source")
echo "$part1"$'\n'"$opening"$'\n'"$additional"$'\n'"$closing"$'\n'"$part2" > "$file"
____

source=script.js
chapter Menggabungkan File '`'$source'`'.
file=../index.php
string="return file_get_contents(__DIR__.'/$source');"
number_1=$(grep -n -F "$string" "$file" | head -1 | cut -d: -f1)
e Mengganti baris '`'"$string"'`'
number_1plus=$((number_1 - 1))
number_1plus2=$((number_1 + 1))
part1=$(sed -n '1,'$number_1plus'p' "$file")
part2=$(sed -n $number_1plus2',$p' "$file")
opening="        return <<<'EOF'"
closing="EOF;"
additional=$(<"$source")
echo "$part1"$'\n'"$opening"$'\n'"$additional"$'\n'"$closing"$'\n'"$part2" > "$file"
____

file=../index.php
chapter Menjadikan compact file '`'$file'`'.
code sed -i \''/^$/d'\' \""$file"\"
code sed -i \''s/[[:space:]]*$//'\' \""$file"\"
sed -i '/^$/d' "$file"
sed -i 's/[[:space:]]*$//' "$file"
____
