# itc-ua-parser

* run

    $ docker-compose build && docker-compose up

* using another console tab/window run command below

    $ docker-compose exec app bash

* to parse something use command app:parse like this

    root@6a3bbd3ea2c6:/var/www# php bin/console app:parse https://itc.ua/
