/Applications/MAMP/Library/bin/mysqldump --no-create-info  -uroot -proot storeLocal lilworks_orderstep lilworks_country lilworks_warranty lilworks_shipping_methods_countries lilworks_shipping_method   lilworks_payment_modality lilworks_payment_method   fos_user  lilworks_customer  lilworks_address lilworks_phonenumber  lilworks_order lilworks_orders_realshippingmethods lilworks_orders_paymentmethods  lilworks_orders_products   lilworks_orders_ordersteps lilworks_ordersProducts_taxes lilworks_order_products_warranties   > /tmp/dumpLocalToDistant.sql;
/Applications/MAMP/Library/bin/mysql SET FOREIGN_KEY_CHECKS=0;
/Applications/MAMP/Library/bin/mysql  --force  -h92.243.27.44 -P3306 -ustoreLocal -pQG2xntggDbU6PoEt storeLocal  < /tmp/truncator.sql;
/Applications/MAMP/Library/bin/mysql  --force  -h92.243.27.44 -P3306 -ustoreLocal -pQG2xntggDbU6PoEt storeLocal  < /tmp/dumpLocalToDistant.sql;
/Applications/MAMP/Library/bin/mysql  SET FOREIGN_KEY_CHECKS=1;