# Skaleet Interview

Ce projet contient un ensemble de tests techniques pour les candidats à un poste de Développeur chez Skaleet

[Documentation interne associée (n'est pas destinée au candidat)](https://tagpay.atlassian.net/wiki/spaces/RD/pages/2526445703/Interview+d+veloppeur)


# Pré-requis

- installer PHPStorm
- installer Docker et/ou PHP 8.0 avec composer

# Installation

- clonez le projet : `git clone git@gitlab.com:skaleet-public/interview/interview.git`
- positionnez vous dans le dossier : `cd interview`
- installez les dépendances `composer install` ou `docker-compose run install`

# Critères d'évaluation

Pour cet exercice votre priorité est de développer un code lisible, testé et maintenable.
Nous évaluerons vos connaissances des principes SOLID, vos compétences en tests automatisés, architecture hexagonale et les tactical patterns du Domain Driven Design.

Il n'est pas nécessaire d'avoir implémenté toutes les règles de gestion pour réussir ce test.
Nous préférons un candidat qui n'implémente pas toutes les règles, mais qui livre un code dont il est fier.



# Exercice #1 :  Pay by card

## Description du use case

Un client se rend chez un commerçant et souhaite régler ses achats par carte bancaire.
Il positionne la carte sur le terminal de paiement et une requête est envoyée au système pour valider la transaction.

Vous devez implémenter la logique métier qui se déclenche lorsqu'un tel appel arrive sur le système.
Voici la liste des règles de gestions à implémenter :
- Le montant fourni en entrée est strictement positif.
- La devise des comptes impactés et du paiement doivent être identiques.
- Le compte du client est débité du montant de la transaction.
- Le compte du commerçant est crédité du montant de la transaction.
- La date de la transaction est la date courante au moment du paiement.

**Attention** : les montants sont modélisés en centimes. Donc `100` vaut `1.00 €`.

## Critères d'acceptance

Le solde des comptes est mis à jour en fonction des paramètres de la transaction.
La transaction est historisée ainsi que les mouvements réalisés sur les comptes.

## Exemple 1

- Un client a un solde de 150€
- Un commerçant a un solde de 2 500€
- La banque a un solde 10 000€

Le client fait un paiement de 15.36 €

|                 | Compte du client | Compte du commerçant |
|-----------------|------------------|----------------------|
| *solde initial* | 150 €            | 2 500 €              |
| *paiement*      | -15.36 €         | +15.36 €             |
| *solde final*   | 134.64 €         | 2 515.36 €           |


# Environnement existant

## Classes à disposition
- Le comportement décrit doit être implémenté dans la méthode `PayByCardCommandHandler::handle()`
- Le projet expose une commande CLI  (`PayByCardCli`) permettant de lancer le use case.
- Le projet expose une classe `PayByCardCommandHandlerTest` et un fichier `phpunit.xml` permettant de lancer les tests unitaires du projet (et calculer le code coverage)


## Contraintes
- La classe `PayByCardCommand` ne doit pas être modifiée
- Le nom et les paramètres fournis à la méthode `PayByCardCommandHandler::handle()` ne doivent pas être modifiés
- Le comportement et la signature des méthodes existantes de la classe `InMemoryDatabase` ne doivent pas être modifiés. Il est possible d'y ajouter de nouvelles méthodes si besoin
- Hormis les classes spécifiées ci-dessus, n'importe quelle autre classe peut être modifiée/ajoutée/supprimée


## Lancer les tests

- `./vendor/bin/phpunit`

ou

- `docker-compose run test`

## Lancer le use case

- `php bin/console.php pay {clientAccountNumber} {amount} {currency} {merchantAccountNumber}`

ou

- `docker-compose run console pay {clientAccountNumber} {amount} {currency} {merchantAccountNumber}`

## Gérer la base de données
Il s'agit d'une base de donnée sous le format d'un fichier contenant des objets PHP sérialisés.
Le fichier s'appelle `db` à la racine du projet.

Deux commandes permettent d'interagir avec :
- `php bin/console.php database:dump` ou `docker-compose run console database:dump` : pour visualiser son contenu
- `php bin/console.php database:clear` ou `docker-compose run console database:clear` : pour la remettre dans l'état initial


# Exercice #2 : pour aller plus loin, gestion des frais
Cet exercice n'est pas a réaliser lors du test technique.
Il est là pour un contexte de training interne.

## Description du use case

Par rapport au use case développé dans l'exercice 1, ajouter les règles de gestion suivantes :

- Un frais de 2% du montant de la transaction est appliqué lors de l'opération. Le compte du commerçant est débité du
  montant de ces frais, et le compte de la banque est crédité.
- Les frais sont plafonnés à 3€ maximum.
- Le solde du client ne peut pas être inférieur à 0€.
- Le solde du commerçant ne peut pas être supérieur à 3 000 €
- Le solde du commerçant ne peut pas être inférieur à -1 000 €

### Exemple 2

- Un client a un solde de 150€
- Un commerçant a un solde de 2 500€
- La banque a un solde 10 000€

Le client fait un paiement de 15.36 €

|                 | Comte de la banque | Compte du client | Compte du commerçant |
|-----------------|--------------------|------------------|----------------------|
| *solde initial* | 10 000 €           | 150 €            | 2 500 €              |
| *paiement*      |                    | -15.36 €         | +15.36 €             |
| *frais*         | +0.31 €            |                  | -0.31 €              |
| *solde final*   | 10 000.31 €        | 134.64 €         | 2 515.05 €           |
