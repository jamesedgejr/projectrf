## Operating System ##

The frame work I created works with LAMP (Linux, Apache, MySQL, PHP). The easiest way to get up and running is to download and install VirtualBox (http:// http://www.virtualbox.org/wiki/Downloads) or VMWare Player (http:// www.vmware.com/go/downloadplayer) and obtain a virtual appliance. The appliance that I have used is the LAMP Stack Appliance from TurnKey (http://www.turnkeylinux.org/lamp). When you first boot the appliance you will be asked to set the system root and MySQL root passwords. You can access webmin and phpadmin by connecting to the server via a browser.

Once the virtual appliance is setup you will need to install some additional packages so projectRF will function properly.

```
#apt-get update 
#apt-get autoremove 
#apt-get -y install php-db php5-gd php5-xsl
```
I'm working on switching all code to PDO for the MySQL queries but for the time being you still need php-db.

## Download Latest Version ##

Always obtain the latest code from Git.  I make changes all the time and sometimes they are small fixes that I forget to document.
```
#cd /var/www
#git clone https://code.google.com/p/projectrf/ projectRF
```

Change the ownership of the projectRF folder for security reasons but also to be able to create downloadable reports.
```
# chown -R www-data:www-data projectRF
```

## MySQL Database ##

You will need to log into MySQL and create a database. I called mine projectRF.
```
#mysql –u root –p 
Enter Password: 
mysql>create database projectRF; 
mysql>grant select,insert,update,delete,index,create temporary tables on projectRF.* to projectRF@localhost identified by 'projectRF';
mysql>quit 
```

The command above works with the default config.php (in main folder) of projectRF.  If you set a different database, user or password you need to update that file.

Move to the projectRF directory and execute the sql file to create the database tables.  Then delete the file.

```
# cd /var/www/projectRF
# mysql -u root -p projectRF < projectRF.sql 
Enter Password:
# rm projectRF.sql
```

## Composer ##

Install Composer, a dependency manager for PHP.  https://getcomposer.org/
```
# cd /var/www/projectRF
# curl -sS https://getcomposer.org/installer | php
# mv composer.phar /usr/local/bin/
```

## Valitron ##

Valitron is a simple, minimal and elegant stand-alone validation library with NO dependencies. (from the site) https://github.com/vlucas/valitron

```
# cd /var/www/projectRF
# composer.phar require vlucas/valitron
Using version ~1.2 for vlucas/valitron
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
  - Installing vlucas/valitron (v1.2.2)
    Downloading: 100%

Writing lock file
Generating autoload files
```

## Modify php.ini ##

You may have to modify the php.ini file to accept larger upload file sizes, increase the maximum execution time, and max input variable acceptance. The default configuration allows for a total POST size of 16M, a max file upload size of 8M, a maximum execution size of only 30 seconds, and a max input (for GET/POST/COOKIE) of 1000 variables. This is insufficient for large scans that require a lot of parsing, report generation time, with a large number of hosts scanned.

The variables post\_max\_size (line 674), upload\_max\_filesize (line 802), max\_execution\_time (line 386), max\_input\_time (line 396) and memory\_limit (line 407) should be modified to allow for larger XML files.  max\_input\_vars (line 403) should be changed from 1000 to something larger like 10000.  If this is not modified the scripts will quietly stop at 1000.

All of these settings can be found in the following section of the php.ini file.
```
 ;;;;;;;;;;;;;;;;;;;
 ; Resource Limits ;
 ;;;;;;;;;;;;;;;;;;;
```
By default the VIM install on the appliance does not show line numbers.  When you first enter VIM you should enter a colon (:) and hit enter. Then type the command set number and hit enter. You will now have line numbers. Google is your friend for finding out how to use VIM.
`#vim /etc/php5/apache2/php.ini `
I set each file size value and memory limit to 1G and the execution time to 1800 (30 minutes). I’ve parsed 1G WebInspect and AppDetective files that large without issue.
Restart the Apache webserver to load the modified php.ini file
`#/etc/init.d/apache2 restart `

or

`#service apache2 restart `

### Modify my.cnf (Optional) ###

You may want to connect to the projectRF database from a remote IP address.  I do this for my development but you may want to run your own queries against the data that is parsed and loaded into the tables.  This is especially the case for Kismet, Nmap, and Dumpsec.  In order to connect from a remote IP you have to modify the MySQL configuration file my.cnf in /etc/mysql and change bind-address to 0.0.0.0. (see http://blog.ubiq.co/enable-remote-access-mysql/)

You will also need to grant access to the remote IP address you are connecting from.  Or you can be less secure and grant access to all IP addresses.  You can be even more insecure by granting all privileges to the user account.

```
#mysql –u root –p 
Enter Password: 
mysql>create database projectRF; 
mysql>grant all privileges on projectRF.* to root@'%' identified by 'mypasswd';
mysql>quit 
```