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

Pull dan rebase.

```
git add tinyfilemanager.php
git commit -m "update patch"
git pull origin master --rebase
```

Copy PHP Script.

```
cp tinyfilemanager.php /var/www/project/$domain/scripts/tinyfilemanager
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

## Finish

Total terdapat minimal 6 host.
 - https://$domain/ untuk tempat login user atau admin.
 - https://admin.$domain/ untuk management seluruh file.
 - https://$user-private.$domain/ for manage user's file inside private directory.
 - https://$user-public.$domain/ for manage user's file inside public directory.
 - https://$user.$domain/ untuk destinasi link share file user untuk public.
 - https://public.$domain/ untuk destinasi link share file global untuk public.

## Admin

Untuk menambah user, visit:

https://admin.$domain/scripts/adduser.php

atau https://admin.$domain/adduser

Untuk mengedit password user, visit:

https://admin.$domain/scripts/passwd.php

atau https://admin.$domain/passwd

Untuk menghapus user, visit:

https://admin.$domain/scripts/deluser.php

atau https://admin.$domain/deluser

Untuk logout, visit:

https://admin.$domain/scripts/logout.php

atau https://admin.$domain/logout

To show excluded items, you must add key query `all` in URL.
 - https://admin.$domain/?all
 - https://admin.$domain/storage/$user/?all
 - https://admin.$domain/scripts/?all

## Public

Public repository berada pada alamat `https://public.$domain/` dan point ke
directory `/var/www/project/$domain/public`.

Untuk menonaktifkan public repository, buat file kosong bernama `404.html`
didalam folder (direktori) pada level pertama.

```
cd /var/www/project/$domain/public
touch 404.html
```

Untuk menonaktifkan listing file pada salah satu folder (direktori) pada
public repository, buat file kosong bernama `403.html` pada folder tersebut.

Untuk mengaktifkan tampilan gallery pada salah satu folder (direktori) pada
public repository, buat file kosong bernama `gallery.html` pada folder tersebut.

## User

User yang ingin memindahkan file dari dan ke `private` dan `public` dapat
melakukan nya di host `https://$user-private.$domain/`. Terdapat
symbolic link ke directory `public`.

Menu untuk user, terdapat di halaman utama https://$domain/. Terdapat menu untuk
logout dan ganti password. Yakni:
 - https://$domain/menu/password
 - https://$domain/menu/logout

Untuk menonaktifkan user public repository, buat file kosong bernama `404.html`
didalam folder (direktori) pada level pertama.

Untuk menonaktifkan listing file pada salah satu folder (direktori) pada user
public repository, buat file kosong bernama `403.html` pada folder tersebut.

Untuk mengaktifkan tampilan gallery pada salah satu folder (direktori) pada user
public repository, buat file kosong bernama `gallery.html` pada folder tersebut.

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

## Optional

Turn user public folder into gallery.

```
domain=ui.web.id
```

Optional. Clone InstaGallery repo.

```
cd /tmp
git clone https://github.com/stuporglue/InstaGallery.git
cd InstaGallery
```

Pindah ke commit `dfa6925` (Mon Nov 23 21:58:29 2020 -0600) dan update patch.

```
git checkout dfa6925
git apply /var/www/project/$domain/scripts/InstaGallery/update.patch
```

Copy PHP Script.

```
cp index.php /var/www/project/$domain/scripts/InstaGallery
```

## Optional 2

Buat user public.

```
cd /var/www/project/$domain/scripts
./adduser.sh public
```

Anda bisa mendelegasikan public repository ke user terpisah tanpa akses admin.

## Optional 3

Terdapat tiga direktori sebagai berikut:

 - Installation Directory, default value adalah di: `/var/www/project/$domain`
 - User Storage Directory, default value adalah di: `/var/www/project/$domain/storage`
 - Public Storage Directory, default value adalah di: `/var/www/project/$domain/public`

Ketiga value diatas dapat diubah dengan cara edit file `config.php`.

```
cd /var/www/project/$domain/scripts
vi config.php
```

atau buat file override config, yakni `config.local.php`.

```
cd /var/www/project/$domain/scripts
vi config.local.php
```

Kemudian perbarui informasi konfigurasi nginx virtual host dengan cara:

```
cd /var/www/project/$domain/scripts
./nginx-build-config.sh > /etc/nginx/sites-available/$domain
nginx -s reload
```
