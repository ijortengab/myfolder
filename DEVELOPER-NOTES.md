Developer Task
--------------

- [x] Pastikan user bisa mengakses route `/index.php/___pseudo/user/login`
      karena secara UX, ada saja user yang menge-klik open in new tab.
      File yang perlu dikerjakan adalah `/src/MyFolder/Module/User/UserController.php`.
- [x] Pastikan property `lastIndexShown` pada object Modal dapat diisi.
- [x] Buat fitur Dashboard.
- [x] Tombol logout kita set hanya ada di dashboard.
- [ ] Twig processor agar bisa merender seperti ini `{{ value.top ? checked }}`
      untuk mendukung radio button pada form.
- [ ] Index dapat dibuat pagerize, bisa otomatis atau diset manual.

Release
-------

Saat release nanti, setidaknya dihadirkan 4 macam contoh MyFolder sesuai use case.

1. 01-myfolder-as-directory-listing/index.php
2. 02-myfolder-as-filemanager/index.php
3. 03-myfolder-as-blog-static-generator/index.php
4. 04-myfolder-as-cloud-storage/index.php

Module Future
-------------
 - static_site_generator
 - markdown
 - config_file
 - config_file_per_user
 - chroot
 - file_operations
 - upload
 - upload_remote
 - access_control

Contoh User Storage
-------------------
~{user}
@{user}
+{user}

https://icon-icons.com/id/download/113445/ICO/32/

Command mengubah massal class name
----------------------------------

```
grep -r -l HtmlElementEvent
grep -r -l HtmlElementEvent | while IFS= read line; do sed s,HtmlElementEvent,IndexInvokeHtmlElementEvent,g -i "$line"; done
mv MyFolder/Module/Index/HtmlElementEvent.php MyFolder/Module/Index/IndexInvokeHtmlElementEvent.php
git add MyFolder/Module/Index/IndexInvokeHtmlElementEvent.php

grep -r -l HtmlElementSubscriber
grep -r -l HtmlElementSubscriber | while IFS= read line; do sed s,HtmlElementSubscriber,IndexInvokeHtmlElementSubscriber,g -i "$line"; done
find -type f -iname HtmlElementSubscriber\.php
find * -type f -iname HtmlElementSubscriber\.php | while IFS= read line; do dirname=$(dirname "$line"); mv "$line" "$dirname"/IndexInvokeHtmlElementSubscriber.php; done

git commit -m 'Mengubah class name dari HtmlElementEvent menjadi IndexInvokeHtmlElementEvent. Mengubah class name dari HtmlElementSubscriber menjadi IndexInvokeHtmlElementSubscriber.'

```
