#!/bin/bash
set -e

mysql_client_ssl_flag='--skip-ssl'

importSQLFile() {
    mysql --default-character-set=utf8mb4\
        --init-command="SET NAMES UTF8;"\
        $mysql_client_ssl_flag\
        -u "$MYSQL_USER"\
        -h "$MYSQL_HOST"\
        -p"$MYSQL_PASSWORD"\
        "$MYSQL_DATABASE"\
        < "$1"

}

if [ "$(mysql $mysql_client_ssl_flag -u "$MYSQL_USER" -h "$MYSQL_HOST" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "show tables;" --batch | wc -l)" -eq 0 ]; then

    # Check if demodata is required
    if [ ! -z $DEMO_DATA ]; then
        echo "INSTALL DEMODATA"
        importSQLFile ./db/studip-demo-installation.sql
    else
        echo "INSTALL DB"
        importSQLFile ./db/studip-basic-installation.sql

        echo "INSTALL ROOTUSER"
        importSQLFile ./db/studip-root-user.sql
    fi

    echo "INSTALLATION FINISHED"
else
    echo "Found some SQL table. Skipping installation"
fi
