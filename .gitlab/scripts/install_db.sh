#!/bin/bash
set -e

importSQLFile() {
    mysql --default-character-set=utf8mb4\
        -u $MYSQL_USER\
        -h $MYSQL_HOST\
        -p$MYSQL_PASSWORD\
        $MYSQL_DATABASE\
        < $1

}

if [ $(mysql -u $MYSQL_USER -h $MYSQL_HOST -p$MYSQL_PASSWORD $MYSQL_DATABASE -e "show tables;" --batch | wc -l) -eq 0 ]; then

    # Setup mysql database
    echo "INSTALL DB"
    importSQLFile ./db/studip.sql
    echo "INSTALL DEFAULT DATA"
    importSQLFile ./db/studip_default_data.sql
    importSQLFile ./db/studip_resources_default_data.sql

    echo "INSTALL ROOTUSER"
    importSQLFile ./db/studip_root_user.sql

    # Check if demodata is required
    if [ ! -z $DEMO_DATA ]; then
        echo "INSTALL DEMODATA"
        importSQLFile ./db/studip_demo_data.sql
        echo "INSTALL MVV_DEMODATA"
        importSQLFile ./db/studip_mvv_demo_data.sql
        echo "INSTALL RESOURCES-DEMODATA"
        importSQLFile ./db/studip_resources_demo_data.sql
    fi

    echo "INSTALLATION FINISHED"
else
    echo "Found some SQL table. Skipping installation"
fi
