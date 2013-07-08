[![Dependency Status](https://www.versioneye.com/user/projects/51bc76305e594d0002016bc8/badge.png)](https://www.versioneye.com/user/projects/51bc76305e594d0002016bc8)

Installation de FiftySent
=========================

Récupérer les sources
---------------------

    git clone git://github.com/wanadev/fiftysent.git

Paramétrages de base
--------------------

    cp app/config/parameters.yml.example app/config/parameters.yml

Récupérer Composer
------------------

    curl -sS https://getcomposer.org/installer | php

Installation des vendors
------------------------

Vérifiez que l'extension `phar.so` et `zip.so` sont activées dans votre `php.ini`.

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
