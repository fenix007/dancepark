#!/usr/bin/env bash
HTTPDUSER=www-data
sudo /usr/bin/setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX web app/cache app/logs
sudo /usr/bin/setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX web app/cache app/logs

