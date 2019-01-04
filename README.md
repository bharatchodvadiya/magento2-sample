# magento2-sample
This repository contains sample code for creating magento2 sample module.

Follow below steps after creating new magento 2 module.

cd c:/xampp/htdocs/magento
rm -rf var/cache/*
rm -rf var/page_cache/*
php bin/magento indexer:reindex
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
