Installation de Sendbox
=======================

Récupérer les sources
---------------------

    git clone git://github.com/wanadev/sendbox.git

Paramétrages de base
--------------------

    mv app/config/parameters.ini.example app/config/parameters.ini

Adaptez la configuration à vos besoins.

    mkdir app/cache app/logs
    chmod 777 app/cache app/logs

Installation des vendors
------------------------

Vérifiez que l'extension phar.so et zip.so est activée dans votre php.ini.

    php bin/vendors install

Création du répertoire d'upload
-------------------------------

    mkdir web/bundles/sbsendbox/uploads
    chmod 777 web/bundles/sbsendbox/uploads

Ready!

### Wiki ###

* [Installation de ZipArchive](https://github.com/wanadev/sendbox/wiki/Installation-de-ZipArchive)
* [Comment contribuer](https://github.com/wanadev/sendbox/wiki/Comment-contribuer)