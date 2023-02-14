# MyFolder

This Repository is configuration of https://public.myfolder.my.id.

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

Install certbot, and request ceritificate for $domain dan auto install.

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

Jika berhasil, maka harusnya ada file certificate.

```
if [ -f /etc/letsencrypt/live/$domain/fullchain.pem ];then echo file exist.; fi
```

Request ceritifacte untuk wildcard subdomain dan tidak perlu auto install.

```
certbot certonly -d '*.'"$domain"
```

Atau bagi pengguna VPS Digital Ocean. Simpan token pada file `~/token`.

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

Jika berhasil, maka harusnya ada file certificate.

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
mv src/var/www/project/myfolder.my.id \
   src/var/www/project/$domain
mv src/etc/nginx/sites-available/myfolder.my.id \
   src/etc/nginx/sites-available/$domain
```

Edit file contains domain.

```
grep -r -l 'myfolder\.my\.id' | while IFS= read line; do \
    sed 's|myfolder\.my\.id|'$domain'|g' -i "$line"
done
```

Edit file nginx config which contains domain variant regex.

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

Enable virtual host and reload nginx.

```
cd /etc/nginx/sites-enabled/
ln -s ../sites-available/$domain
nginx -s reload
```

Visit website then log in as admin.

```
https://$domain/
```

or direct via URL.

```
https://admin:$password@$domain/
```

if logged in, you'll redirect to https://admin.$domain/

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

## Finish

Total terdapat minimal 6 host.
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

Public repository berada pada alamat `https://public.$domain/` dan point ke
directory `/var/www/project/$domain/public`.

Untuk menonaktifkan public repository, buat file kosong bernama `404.html`.

```
cd /var/www/project/$domain/public
touch 404.html
```

## Issue

Belum di test untuk CSRF.

## Tips

Bagaimana menjadikan folder di laptop, agar dapat diakses melalui cloud?

Misalnya kita menggunakan skenario sbb:

 - domain yang digunakan adalah `myfolder.my.id`
 - user yang akan digunakan adalah `laptop`
 - penyimpanan akan menggunakan scope `private`
 - directory remote adalah `mnt`, relative terhadap private.

Maka:

Buat mount point.

```
mkdir -p                    /var/www/project/myfolder.my.id/storage/laptop/private/mnt
chown -R www-data:www-data  /var/www/project/myfolder.my.id/storage/laptop
```

Dari local, kita `ssh` login ke server dan buat remote port forwarding.

```
ssh root@myfolder.my.id -R 22000:127.0.0.1:22
```

Dari cloud/server, kita sshfs ke local melalui port forwarding.

```
sshfs -p 22000 -o allow_other \
    ijortengab@127.0.0.1:/cygdrive/c/Users/ijortengab \
    /var/www/project/myfolder.my.id/storage/laptop/private/mnt
```

Sesuikan path source dan path target relative dari server remote. Command diatas menggunakan:

 - path source: `ijortengab@127.0.0.1:/cygdrive/c/Users/ijortengab`
 - path target: `/var/www/project/myfolder.my.id/storage/laptop/private/mnt`

Jika semua berjalan lancar, maka sekarang direktori laptop local dapat diakses melalui URL:

```
https://laptop-private.myfolder.my.id/mnt/
```

Untuk unmount, gunakan command sbb:

```
fusermount -u /var/www/project/myfolder.my.id/storage/laptop/private/mnt
```
