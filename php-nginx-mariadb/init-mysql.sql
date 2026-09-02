CREATE DATABASE IF NOT EXISTS `jujiashizhi` DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

grant all privileges on `jujiashizhi`.* to `jujiashizhi`@'%' identified by '17188c5fdee5692e';
DROP USER 'root'@'%';

flush privileges;

# 正在加载数据的时候，连接可以回被拒绝
use jujiashizhi;
source /var/www/tmp/old_data.sql
