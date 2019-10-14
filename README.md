# sftp-web
A simple PHP web interface for monitoring the status of logged in users on an SFTP server

### Using /etc/shadow for .htaccess authentication

Install the external auth packages (on CentOS 7)

```
sudo yum install mod_authnz_external pwauth
```

pwauth setuid permissions:

```
chmod u+s /usr/bin/pwauth
```

Update your Apache config:

```
<IfModule mod_authnz_external.c>
  DefineExternalAuth pwauth pipe /usr/sbin/pwauth
</IfModule>
```

.htaccess file:

```
AuthType Basic
AuthName "Login"
AuthBasicProvider external
AuthExternal pwauth
Require valid-user
# or you can use specific users
# Require user tom dave
```

Restart Apache
