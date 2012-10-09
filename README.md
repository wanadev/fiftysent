[![Build Status](http://jenkins.wanadev.org/job/FiftySent/badge/icon)](http://jenkins.wanadev.org/job/FiftySent/)

Installation de FiftySent
=========================

Récupérer les sources
---------------------

    git clone git://github.com/wanadev/fiftysent.git

Paramétrages de base
--------------------

    mv app/config/parameters.yml.example app/config/parameters.yml

Récupérer Composer
------------------

    $ curl -s https://getcomposer.org/installer | php

Installation des vendors
------------------------

Vérifiez que l'extension _phar.so_ et _zip.so_ sont activées dans votre _php.ini_.

    php composer.phar install

Création du répertoire d'upload
-------------------------------

    mkdir web/uploads
    chmod 777 web/uploads

Ready!

### Wiki ###

* [Installation de ZipArchive](https://github.com/wanadev/fiftysent/wiki/Installation-de-ZipArchive)
* [Comment contribuer](https://github.com/wanadev/fiftysent/wiki/Comment-contribuer)
* [Versions](https://github.com/wanadev/fiftysent/wiki/Versions)
