# RC_Monitor

    0. INTRODUCTION
    1. REQUIREMENTS
    2. INSTALLATION
    3. LICENSE
    4. AUTHOR
    5. REFERENCES
    6. CONTRIBUTING


## 0. INTRODUCTION

This plugin makes a monitoring of resources of roundcube using it self core/libs ( try to authenticate in imap, execute a select in database, make write/read memcache test ) and expose results using a JSON.

## 1. REQUIREMENTS

roundcube ~> 0.8.7


## 2. INSTALLATION

Download the plugin and extract into plugin dir:

```
cd roundcube/plugins/

curl -L "https://github.com/locaweb/rc_monitor/archive/master.zip" -o rc_monitor.zip
or clone with git...( git clone git@github.com:locaweb/rc_monitor.git )
unzip rc_monitor.zip
```

To enable url for monitor, add the content to virtualhost:

## APACHE:

```

<VirtualHost *:80>
    # ...etc...
    #
    <Location /monitoring >
            order deny,allow
            deny from all
            allow from 127.0.0.1
            RewriteEngine on
            RewriteCond %{REQUEST_URI} ^/monitoring$
            RewriteRule (.*) /plugins/rc_monitor/rc_monitor.php [L]
    </Location>
</VirtualHost>

```


On plugin directory, copy the config.inc.php.dist to config.inc.php and configure it!

Use your monitor from localhost to:

```
http://webmail.domain.com/monitoring
http://webmail.domain.com/monitoring?functional=true {more tests and details}
```

If you need access from other hosts, change the allow rule of Location.


## 3. LICENSE

```
Copyright (c) 2016 Thiago Coutinho <thiago@osfeio.com>
<thiago.coutinho@locaweb.com.br>

Permission to use, copy, modify, and distribute this software for any
purpose with or without fee is hereby granted, provided that the above
copyright notice and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
```

## 4. AUTHOR

RC_Monitor was developed by Thiago Coutinho in Locaweb
(http://www.locaweb.com.br).


## 5. REFERENCES

http://www.sitepoint.com/parsing-xml-with-simplexml/
http://code.google.com/p/sabredav/wiki/WebDAVClient#Doing_a_PROPFIND_request
http://www.ietf.org/rfc/rfc4791.txt
http://php.net/memcache
