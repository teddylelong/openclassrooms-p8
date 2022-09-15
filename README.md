# OpenClassrooms - Projet 8

Améliorez une application existante de ToDo & Co

[![SymfonyInsight](https://insight.symfony.com/projects/1676b05a-a5ac-4404-951d-10d3bbf94c96/mini.svg)](https://insight.symfony.com/projects/1676b05a-a5ac-4404-951d-10d3bbf94c96)

Ce dépot est un projet étudiant en cours de réalisation dans le cadre de ma formation *Développeur d'Applications PHP/Symfony* avec OpenClassrooms.

### Pré-requis :

- Installez [Docker](https://docs.docker.com/get-docker/)
- Installez [Composer](https://getcomposer.org/download/)

Ce projet fonctionne sous PHP `8.1` et Symfony `6.1`. L'image Docker fournie vous permettra de
faire fonctionner l'environement de test complet. Le gestionnaire de dépendances Composer
permetra d'installer sur cet environement l'ensemble des paquets requis pour le bon
fonctionnement du projet.

## Navigation

Utilisez-les liens suivants afin d'accéder rapidement aux ressources liées au projet :

- [Diagrammes UML](https://github.com/teddylelong/openclassrooms-p8/tree/main/UML) :
  1. [Cas d'utilisation](https://github.com/teddylelong/openclassrooms-p8/tree/main/UML/01-cas-utilisation)
  2. [Séquentiels](https://github.com/teddylelong/openclassrooms-p8/tree/main/UML/02-sequences)
  3. [MCD](https://github.com/teddylelong/openclassrooms-p8/blob/main/UML/03-MCD.png)
  4. [MPD (MySQL)](https://github.com/teddylelong/openclassrooms-p8/blob/main/UML/04-MPD.png)
- [Issues du projet](https://github.com/teddylelong/openclassrooms-p8/issues?q=is%3Aissue+is%3Aclosed)
- [Contribuer au projet, mode d'emploi](https://github.com/teddylelong/openclassrooms-p8/blob/main/CONTRIBUTING.md)
- [Rapport de couverture de code généré par PHPUnit](https://htmlpreview.github.io/?https://github.com/teddylelong/openclassrooms-p8/blob/main/todo_and_co/public/test-coverage/index.html)

## Arborescence du projet

Trois dossiers se trouvent à la racine du projet :
- `php` : Lié à Docker - contient le fichier de configuration vhost recommandé par Symfony
- `todo_and_co` : Dossier racine du projet web
- `UML` : Contient l'ensemble des diagrammes UML relatifs au projet

## Comment l'installer ?

Suivez les étapes ci-dessous afin d'effectuer une installation locale de ce projet.


### 1. Clonez le projet

Depuis le terminal de votre ordinateur, utilisez la commande suivante afin de copier
l'intégralité des fichiers du projet dans le dossier de votre choix :

```
cd {chemin/vers/le/projet}
git clone https://github.com/teddylelong/openclassrooms-p8.git
```

### 2. Initialisez les conteneurs Docker

Depuis le dossier racine du projet, lancez la commande suivante :

```
cd {chemin/vers/le/projet/}openclassrooms-p8/
docker-compose build
docker compose up -d
```

### 3. Installation des dépendances

Afin d'installer Symfony et l'ensemble de ses dépendances, nous allons désormais 
lancer les commandes directement depuis le conteneur Docker, pour des raisons pratiques :

```
docker exec -it td_www bash
cd todo_and_co/
composer install
```

### 4. Création des bases de données

Toujours depuis le conteneur Docker, exécutez les commandes suivantes afin 
d'initialiser la base de données principale :

```
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```
Validez en saisissant « y ». La base de données est à présent prête !

Nous devons maintenant initialiser la base de données de l'environement de test, car elle nous
sera utile pour l'exécution des tests fonctionnels. Toujours depuis le conteneur Docker,
lancez les commandes suivantes :

```
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update --force --env=test
```

### 5. Mise en place des Fixtures

Une fois l'initialisation de la base de donnée terminée, toujours depuis le conteneur Docker,
lancez les commandes suivante afin de charger un jeu d'enregistrements fictifs (Fixtures) 
dans les deux bases de données :

```
php bin/console doctrine:fixtures:load
php bin/console doctrine:fixtures:load --env=test
```
Validez en saisissant « y ».

### Fin de l'installation

Le projet est à présent installé !

- Vous devriez pouvoir le tester en vous rendant sur http://localhost:8000/.
- Accédez à PHPMyAdmin via http://localhost:8080 (Nom d'utilisateur `root` et mot de
  passe vide)
- Lancez les tests unitaires et fonctionnels en exécutant depuis le conteneur 
Docker la commande suivante :
```
php vendor/bin/phpunit
```


## Comptes utilisateurs

Afin de pouvoir tester l'API BileMo et ses fonctionnalités, sont mis à disposition deux comptes clients
qui disposent chacun d'un rôle différent. Utilisez-les comme bon vous semble.


| Nom d'utilisateur | Mot de passe | Rôle       |
|-------------------|--------------|------------|
| test_admin        | 0000         | ROLE_ADMIN |
| test_user         | 0000         | ROLE_USER  |
