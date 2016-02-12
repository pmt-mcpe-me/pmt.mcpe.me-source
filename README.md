# web-server-source
Source code for http://pmt.mcpe.me (partial)

## Requirements
* At least PHP 5.6 (default PHP 5 installation on apt is PHP 5.5, make sure to install PHP 5.6 instead), PHP 7.0+ _recommended_
* PHP YAML extension
* PHP cURL extension
* probably other things that I forgot

## Installation
Clone this repo at the **document root** of your website. It is because some hyperlinks explicitly indicate as `/file.php`. Assuming `/var/www/html` in this page.

Please allow read/write access to the parent directory of the document root of the website, i.e. `/var/www`. I do that with `chown -R www-data:www-data /var/www`.

A GitHub service client\_id + client\_secret is required to be set up at `/var/www/cid.txt` and `/var/www/secret.txt`. The service should redirect the clien to `/insta/accessToken.php`.

A GitHub API access token for any valid account (without any scopes required) is required at /var/www/token.txt

All these three files should contain the tokens/secrets in plaintext. No trimming would be done by the PHP script.

You are recommended to add a file `.git/.htaccess` with the content:

```htaccess
deny from all
```

for the sake of security.

This website generates files at /var/www/html/insta/data/ without deleting them. If you find the directory too resource-consuming, please add a cronjob yourself to clean it.

===

Here is a list of PHP extensions used by the website at pmt.mcpe.me:

```
Array
(
    [0] => Core
    [1] => date
    [2] => ereg
    [3] => libxml
    [4] => openssl
    [5] => pcre
    [6] => zlib
    [7] => bcmath
    [8] => bz2
    [9] => calendar
    [10] => ctype
    [11] => dba
    [12] => dom
    [13] => hash
    [14] => fileinfo
    [15] => filter
    [16] => ftp
    [17] => gettext
    [18] => SPL
    [19] => iconv
    [20] => mbstring
    [21] => session
    [22] => posix
    [23] => Reflection
    [24] => standard
    [25] => shmop
    [26] => SimpleXML
    [27] => soap
    [28] => sockets
    [29] => Phar
    [30] => exif
    [31] => sysvmsg
    [32] => sysvsem
    [33] => sysvshm
    [34] => tokenizer
    [35] => wddx
    [36] => xml
    [37] => xmlreader
    [38] => xmlwriter
    [39] => zip
    [40] => apache2handler
    [41] => yaml
    [42] => Weakref
    [43] => PDO
    [44] => curl
    [45] => json
    [46] => readline
    [47] => mhash
    [48] => Zend OPcache
)
```
