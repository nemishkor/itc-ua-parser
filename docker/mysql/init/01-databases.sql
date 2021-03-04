# create databases
CREATE DATABASE IF NOT EXISTS `news_parser`;

# create root user and grant rights
GRANT ALL ON *.* TO 'root'@'%';
