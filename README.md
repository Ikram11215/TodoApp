# READ ME

## Task Management System (Symfony)

### Description

Le projet **Todo App** est une application web développée avec Symfony permettant de gérer des tâches. Les utilisateurs peuvent afficher, ajouter, compléter et supprimer des tâches. L'application propose des filtres pour rechercher des tâches selon leur nom, priorité et état (en cours, terminée, etc.).

### Fonctionnalités

1. **Affichage des Tâches :**
   - La page d'accueil permet d'afficher une liste des tâches avec des filtres pour rechercher par nom, priorité et état de la tâche.
   
2. **Ajout d'une Tâche :**
   - Les utilisateurs peuvent ajouter une nouvelle tâche en spécifiant son nom et sa priorité.
   
3. **Marquer une Tâche comme Terminée :**
   - Les tâches peuvent être marquées comme terminées en cliquant sur un bouton associé à chaque tâche.
   
4. **Suppression d'une Tâche :**
   - Les utilisateurs peuvent supprimer une tâche.

5. **Connexion et Gestion des Utilisateurs :**
   - Seuls les utilisateurs authentifiés peuvent ajouter des tâches.
   - Deux utilisateurs ont été créés (ils seront précisés ultérieurement).
   - Lorsqu'un utilisateur se déconnecte, les tâches ne sont plus affichées.

### Installation

#### Prérequis

- PHP 8.1 ou version supérieure
- Composer
- Symfony CLI (optionnel mais recommandé)
- Base de données (MySQL, PostgreSQL ou autre)

#### Étapes d'installation

1. **Cloner le repository :**
   ```bash
   git clone https://github.com/Ikram11215/TodoApp.git
   cd task-management-symfony
   ```
2. **Installer les dépendances :**
   ```bash
   composer install
   ```
3. **Configurer l'environnement :**
   - Copier le fichier `.env` en `.env.local` et configurer la connexion à la base de données.

4. **Créer la base de données et exécuter les migrations :**
   ```bash
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate
   ```

5. **Lancer le serveur de développement :**
   ```bash
   symfony server:start
   ```

L'application sera accessible sur `http://127.0.0.1:8000/`.

### Répartition des tâches

- **Ikram** : Création du projet, développement du contrôleur `Task`, ajout d'une tâche.
- **Acil** : Suppression d'une tâche, ajout de la gestion des utilisateurs (connexion), restriction de l'ajout de tâches aux utilisateurs connectés, affichage des tâches uniquement lorsque l'utilisateur est connecté.

### Remarque Finale

Ce projet est notre premier développement avec Symfony. Il n'est pas encore totalement complet et optimisé, car notre objectif principal était d'apprendre et de comprendre les concepts de base du framework. Nous avons fait de notre mieux pour explorer les fonctionnalités essentielles et améliorer nos compétences.

Nous tenons à remercier notre professeur pour son cours clair et structuré, qui nous a servi de guide précieux tout au long de cette expérience d'apprentissage.

