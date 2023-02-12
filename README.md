# MyFolder

This Repository is configuration of https://myfolder.my.id.

The configuration is turn your site in to simple cloud storage.

## Feature

 - Public directory
 - Multi user
 - Private and Public directory per user

## Duplicate to your domain

Buy your domain. Example: `ui.web.id`.

Prerequisite:

 - Create A Record for domain.
 - Create A Record for wildcard subdomain: `*.ui.web.id`.

Login as root.

```
sudo su
```

Set variable of your domain. Example `ui.web.id`.

```
domain=ui.web.id
```

Install nginx.

Setup minimal virtual host for domain.

```
touch /etc/nginx/sites-available/$domain
cd /etc/nginx/sites-enabled
ln -s ../sites-available/$domain
```

Contains of `/etc/nginx/sites-available/$domain`.

```
server {
    listen 80;
    listen [::]:80;
    server_name ui.web.id;
    default_type text/plain;
    return 200 OK;
}
```

Install certbot, and request ceritificate.

```
certbot -i nginx -d $domain
```

Atau bagi pengguna VPS Digital Ocean. Simpan token pada file `~/token`.

```
digitalocean_credentials=$(<~/token)
mktemp=$(mktemp -t digitalocean.XXXXXX.ini)
chmod 0700 "$mktemp"
cat << EOF > "$mktemp"
dns_digitalocean_token = $digitalocean_credentials
EOF
certbot -i nginx \
   --dns-digitalocean \
   --dns-digitalocean-credentials "$mktemp" \
   -d "$domain"
rm "$mktemp"
```

Jika berhasil, maka harusnya ada file certicate.

```
if [ -f /etc/letsencrypt/live/$domain/fullchain.pem ];then echo file exist.; fi
```

Request hanya ceritifacte untuk wildcard subdomain.

```
digitalocean_credentials=$(<~/token)
mktemp=$(mktemp -t digitalocean.XXXXXX.ini)
chmod 0700 "$mktemp"
cat << EOF > "$mktemp"
dns_digitalocean_token = $digitalocean_credentials
EOF
certbot certonly \
   --dns-digitalocean \
   --dns-digitalocean-credentials "$mktemp" \
   -d '*.'"$domain"
rm "$mktemp"
```

Jika berhasil, maka harusnya ada file certicate.

```
if [ -f /etc/letsencrypt/live/$domain-0001/fullchain.pem ];then echo file exist.; fi
```

Clone this repo.

```
cd /tmp
git clone https://github.com/ijortengab/myfolder
cd myfolder
```

Rename directory and file.

```
mv src/var/www/project/myfolder.my.id src/var/www/project/$domain
mv src/etc/nginx/sites-available/myfolder.my.id src/etc/nginx/sites-available/$domain
```

Edit file contains domain.

```
grep -r -l 'myfolder\.my\.id' | while IFS= read line; do \
    sed 's|myfolder\.my\.id|'$domain'|g' -i "$line"
done
```

Edit file nginx config contains domain variant regex.

```
find=$(sed "s/\./\\\./g" <<< "myfolder\.my\.id")
replace=$(sed "s/\./\\\./g" <<< "$domain")
replace=$(sed "s/\./\\\./g" <<< "$replace")
sed -i "s|$find|"$replace"|g" "src/etc/nginx/sites-available/$domain"
```

Verify.

```
grep -F -rn $domain
grep -rn $replace
```

Rsync and preview before.

```
rsync -n -avr src/ /
```

Rsync and execute.

```
rsync -avr src/ /
```

Clone tinyfilemanager repo.

```
cd /tmp
git clone https://github.com/prasathmani/tinyfilemanager.git
cd tinyfilemanager
```

Pindah ke commit `200d9d6` (Sun Feb 5 06:05:07 2023 +0100) dan update patch.

```
git checkout 200d9d6
git apply /var/www/project/$domain/scripts/tinyfilemanager/update.patch
```

Copy PHP Script.

```
cp tinyfilemanager.php /var/www/project/$domain/scripts/tinyfilemanager
```

Kembali ke semula.

```
git switch -
```

Copy Translation.

```
cp translation.json /var/www/project/$domain/scripts/tinyfilemanager
```

Buat user admin.

```
cd /var/www/project/$domain/scripts
./adduser.sh admin
```

Kasih semua permission ke user dimana PHP-FPM berjalan, misalnya `www-data`-nya nginx.

```
chown -R www-data:www-data /var/www/project/$domain
```

Reload nginx.

```
nginx -s reload
```

Kunjungi subdomain admin.

```
https://admin.$domain/
```

## Management User

Untuk menambah user:

https://admin.$domain/scripts/adduser.php

atau https://admin.$domain/adduser

Untuk mengedit password user:

https://admin.$domain/scripts/passwd.php

atau https://admin.$domain/passwd

Untuk menghapus user:

https://admin.$domain/scripts/deluser.php

atau https://admin.$domain/deluser

## FYI

Total terdapat 6 host.
 - https://$domain/ untuk tempat login user.
 - https://admin.$domain/ untuk tempat login admin.
 - https://$user-private.$domain/ untuk management file private.
 - https://$user-public.$domain/ untuk management file public.
 - https://$user.$domain/ untuk destinasi link share file user untuk public.
 - https://public.$domain/ untuk destinasi link share file global untuk public.

User yang ingin memindahkan file dari dan ke `private` dan `public` dapat
melakukan nya di host `https://$user-private.$domain/`. Terdapat
symbolic link ke directory `public`.

Menu untuk user, terdapat di halaman utama https://$domain/. Terdapat menu untuk
logout dan ganti password. Yakni:
 - https://$domain/menu/password
 - https://$domain/menu/logout

## Issue

Belum di test untuk CSRF.
