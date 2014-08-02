nginx-managed-cache-example
===========================

Example showing how to use nginx cache futures.

System
------

Developped and tested on Debian 7, but should work on other Linux distros. In the documentation you can
see config files paths actual for Debian 7.

Code runs on PHP-FPM + nginx. Project uses sqlite as the database.

For demonstation purpose Yii 1.1.15 and Demo Blog app was chosen.

Main Idea
---------

* For caching
[fastcgi](http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html) module is used.
* Insead of cache purging cache updating is used. It is done with combination of
[fastcgi_cache_bypass](http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_cache_bypass)
and [fastcgi_no_cache](http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_no_cache)
directives.
* For cache key page URI is used, which means page URL without GET parameters.
* In project path-formatted URLs are used.
* If you use current page URL, for example in `action` parameter for `form` tag, you have to exclude
cache clear prameter from it.
* Cache clearing is done with HTTP request to the page URL with the addition of GET parameter.
Request is done with [curl](http://php.net/manual/en/book.curl.php).
* nginx was set to prevent cache clear by users. It is alowed to clear cache only with request from
application server.
* You should choose hard to guess GET parameter name. It will prevent unwanted showing of 403 page
for users.
* To disable cache you can set COOKIE with name no_cache to 1.
* To simplify this example cache enabled only for unauthorized users. For authorized users COOKIE with
name no_cache will be set to 1.
* Cache will be cleared after adding or updating post or after comment addition.
* You can temporarily disable cache for unauthorized user to display flash message. To achieve this
set the COOKIE for fiew seconds.

Installation
------------

Let's assume that application runs from `webdev` user with home folder at `/home/webdev`.
Web projects are located an `/home/webdev/www`. Logs for nginx virtualhost are located at
`/home/webdev/www/logs/nginx`. Virtualhost named as `nginx-managed-cache.local` and the files are
located at `/home/webdev/www/nginx-managed-cache.local/web`.

Folders mentioned above must be created. You should correct paths to suit your system. You can
create needed folders with 2 commands:

```bash
mkdir -p /home/webdev/www/logs/nginx
mkdir -p /home/webdev/www/nginx-managed-cache.local/web
```

* Download [project archive](https://github.com/xBazilio/nginx-managed-cache-example/archive/master.zip).
* Unzip the archive and copy content of `nginx-managed-cache-example-master` folder into
`/home/webdev/www/nginx-managed-cache.local/web`.
* Set PHP-FPM to work with `webdev`'s permissions. To achieve this set directives `user` and `group`
in `/etc/php5/fpm/pool.d/www.conf`.
* nginx will comunicate with PHP-FPM through socket. To achieve this set corresponging directives:
```
listen = /var/run/php5-fpm.sock
listen.owner = webdev
listen.group = webdev
listen.mode = 0666
```
* Restart PHP-FPM.
* Add to file `/etc/nginx/nginx.conf` in `http` section folowing string:
```
fastcgi_cache_path /var/lib/nginx/cache levels=1:2 keys_zone=one:10m inactive=1d max_size=256m;
```
* Copy content of `nginx_config` folder in the project into `/etc/nginx`.
* Create link to `/etc/nginx/sites-available/managed-cache-example` in
`/etc/nginx/sites-enabled`.
* Restart nginx.
* Add to `/etc/hosts` folowing string:
```
127.0.0.1 nginx-managed-cache.local
```
* To generate sqlite database go to `/home/webdev/www/nginx-managed-cache.local/web` folder and run:
```bash
php ./app/data/dbgen.php
```
* Open url http://nginx-managed-cache.local/ in your favorite browser.

Check how caching works
-----------------------

In site header you can see date and time with seconds. If you refresh page, you'll see that time is
updating. If you go to the Blog http://nginx-managed-cache.local/post/index, you will see that time
isn't updating because caching is working.

Comments addition will update post's page cache.

If you authorize, cache will be disabled. You can create new posts and update existing ones - cache
for first page of posts list and post's page will be updated.
