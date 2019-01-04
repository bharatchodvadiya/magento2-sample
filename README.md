# magento2-sample
This repository contains sample code for creating magento2 sample module.

Follow below steps after creating new magento 2 module.

cd c:/xampp/htdocs/magento<br>
rm -rf var/cache/*<br>
rm -rf var/page_cache/*<br>
php bin/magento indexer:reindex<br>
php bin/magento setup:upgrade<br>
bin/magento setup:static-content:deploy
