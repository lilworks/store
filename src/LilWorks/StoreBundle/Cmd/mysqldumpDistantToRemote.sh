
/Applications/MAMP/Library/bin/mysqldump -h$1 -P$2 -u$3 -p$4 $5 > /tmp/dumpDistantToRemote.sql;
/Applications/MAMP/Library/bin/mysql -u$6 -p$7 set foreign_key_checks = 0;
/Applications/MAMP/Library/bin/mysql -u$6 -p$7 $8 < /tmp/dumpDistantToRemote.sql;
/Applications/MAMP/Library/bin/mysql -u$6 -p$7 set foreign_key_checks = 1;