# web-server-source
pemapmodder.zapto.org source (partial)

## Requirements
* PHP 5.6 (yes, and the default PHP 5 installation on Ubuntu is PHP 5.5)
* PHP YAML extension
* PHP cURL extension
* probably other things that I forgot

## Installation
Clone this repo at the **document root** of your website. It is because some hyperlinks explicitly indicate as `/file.php`. Assuming `/var/www/html` in this page.

Please allow read/write access to the parent directory of the document root of the website, i.e. `/var/www`. I do that with `chmod -R www-data:www-data /var/www`.

A GitHub service client\_id + client\_secret is required to be set up at /var/www/cid.txt and /var/www/secret.txt

A GitHub API access token for any valid account (without any scopes required) is required at /var/www/token.txt

All these three files should contain the tokens/secrets in plaintext. No trimming would be done by the PHP script.

You are recommended to add a file `.git/.htaccess` with the content:

```htaccess
deny from all
```

for the sake of security.

This website generates files at /var/www/html/insta/data/ without deleting them. If you find the directory too resource-consuming, please add a cronjob yourself to clean it.

