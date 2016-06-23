#!/bin/sh
# File: /var/www/shop/modules/fateak/script/mysql/backup.sh
# Database info
DB_NAME="shop"
DB_USER="root"
DB_PASS="pass"
BACK_DIR="/var/www/shop/application/dblab"
DATE=`date "+%Y%m%d"`
mysqldump --opt -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACK_DIR/$DB_NAME$DATE.sql.gz
DB_NAME="shop_tax"
mysqldump --opt -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACK_DIR/$DB_NAME$DATE.sql.gz
