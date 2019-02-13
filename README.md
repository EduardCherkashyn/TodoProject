# TodoProject

Api doc in project root folder swagger.yaml

Cron

    php bin/console shapecode:cron:scan
 
    php bin/console shapecode:cron:run
    
env EDITOR=nano crontab -e
    
    */1 * * * * php ~/TodoProject/bin/console shapecode:cron:run 