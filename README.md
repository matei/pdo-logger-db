PDO Logger DB
=====================
Logs PDO query log to DB instead of file. Logs can be grouped by "session" and session is managed via CLI

How to install
=== 
Add repository to `composer.json`: 
```json
{
    ...
    "repositories": {
        ...
        "XX": {
            "type": "vcs",
            "url": "https://github.com/matei/pdo-logger-db",
            "canonical": false
        }    
}
```

Then run
```shell
$ composer require matei/pdo-logger-db dev-master
$ bin/magento c:f
$ bin/magento setup:upgrade
```


### How to use
```shell
$ bin/magento dev:db-query-log:enable #to enable logging to DB
$ bin/magento dev:db-query-log:new-session --name add-to-cart # start a new session 
```
Perform the actions you want and then look in the `pdo_log_session` and `pdo_log_line` tables
```shell
$ bin/magento dev:db-query-log:truncate #to clear all the logs from db
$ bin/magento dev:db-query-log:enable #to disable logging to DB 
```


