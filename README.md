# Stubborn - Site e-commerce Symfony

Site e-commerce développé avec Symfony 6.4 pour la marque de sweat-shirts Stubborn.

## Prérequis

- PHP 8.2 ou supérieur (extensions : zip, intl, openssl, mbstring, pdo_mysql, fileinfo, curl)
- Composer
- MySQL 8.0 ou supérieur
- Git

## Installation

1. Cloner le dépôt :

git clone https://github.com/AdelC57/stubborn.git
cd stubborn

2. Installer les dépendances PHP : composer install

3. Créer un fichier `.env.local` à la racine du projet avec vos propres clés Stripe de test (créez un compte gratuit sur https://dashboard.stripe.com pour les obtenir) :

STRIPE*PUBLIC_KEY=pk_test*...
STRIPE*SECRET_KEY=sk_test*...

4. Si besoin, adapter la variable `DATABASE_URL` dans `.env` (ou dans `.env.local`) selon votre configuration MySQL locale.

5. Créer la base de données et exécuter les migrations :

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

6. Charger le jeu de données de démonstration (10 sweat-shirts + compte administrateur) :

php bin/console doctrine:fixtures:load

7. Lancer le serveur local :

php -S 127.0.0.1:8000 -t public public/router.php

8. Ouvrir l'application : http://127.0.0.1:8000/

## Compte administrateur de démonstration

- Email : admin@stubborn.com
- Mot de passe : admin1234

## Réaliser un achat test (Stripe)

- Numéro de carte : 4242 4242 4242 4242
- Date d'expiration : n'importe quelle date future
- CVC : n'importe quel code à 3 chiffres

## Exécuter les tests unitaires php bin/phpunit

## Documentation complète

Voir le fichier `stubborn_documentation.pdf` à la racine du projet.
