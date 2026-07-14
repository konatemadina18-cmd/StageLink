# StageLink

StageLink est une application web Laravel qui aide les candidats a trouver un stage et les recruteurs RH a gerer les offres, les candidatures, les messages et les entretiens.

## Objectif du projet

Le but est de centraliser le processus de recherche de stage :

- le candidat cree son profil et postule aux offres ;
- le RH publie les offres de stage ;
- le RH suit les candidatures et programme les entretiens ;
- le candidat recoit les notifications, les messages et les invitations aux entretiens.

## Roles disponibles

- `candidat` : consulte les offres, postule, gere son profil, ses documents, ses messages et ses notifications.
- `rh_admin` : gere l'entreprise, les offres, les candidatures, les entretiens, l'equipe RH et les messages.
- `assistant` / `rh_user` : aide au suivi des candidatures selon les droits prevus.
- `stagiaire` : role RH limite selon les acces prevus dans le dashboard.

## Fonctionnalites principales

### Cote candidat

- inscription et connexion ;
- dashboard candidat avec cartes cliquables ;
- consultation des offres disponibles ;
- candidature a une offre ou candidature spontanee ;
- suivi des candidatures ;
- liste des entretiens programmes ;
- gestion du profil et du CV principal ;
- messagerie avec les recruteurs ;
- notifications avec badges de nouveaute ;
- preferences : theme, langue, notifications, confidentialite ;
- double authentification par email ou application TOTP.

### Cote RH

- inscription d'une entreprise ;
- dashboard RH ;
- publication, fermeture, reouverture et suppression d'offres ;
- suivi des candidatures ;
- changement du statut d'une candidature ;
- programmation d'entretiens ;
- envoi automatique d'un email d'invitation a l'entretien ;
- messagerie avec les candidats ;
- gestion des collaborateurs RH ;
- parametres du profil et de l'entreprise ;
- double authentification par email ou application TOTP.

## Systeme d'entretien

Quand un RH programme un entretien :

1. la candidature passe au statut `Entretien programme` ;
2. l'entretien est enregistre dans la table `entretiens` ;
3. une notification est creee pour le candidat ;
4. un email professionnel est envoye au candidat avec :
   - l'entreprise ;
   - le candidat ;
   - l'offre ou candidature spontanee ;
   - la date ;
   - l'heure ;
   - le mode ;
   - l'adresse ou le lien ;
   - le recruteur ;
   - la note du RH si elle existe.

## Badges de nouveaute

Les badges servent a montrer les elements non lus ou nouveaux :

- messages non lus ;
- notifications non lues ;
- changements sur les candidatures ;
- entretiens programmes.

Quand le candidat ouvre une section, StageLink marque les elements comme vus et remet le badge a zero.

## Double authentification

StageLink garde la double authentification par email et ajoute la double authentification par application.

Applications compatibles :

- Google Authenticator ;
- Microsoft Authenticator ;
- Authy.

La methode par application utilise le protocole TOTP avec QR Code et cle secrete.

## Technologies utilisees

- Laravel 13 ;
- PHP 8.3 ;
- MySQL ;
- Blade ;
- JavaScript ;
- Composer ;
- `pragmarx/google2fa` pour les codes TOTP ;
- `bacon/bacon-qr-code` pour generer les QR Codes.

## Installation locale

Installer les dependances PHP :

```bash
composer install
```

Copier le fichier `.env` si necessaire :

```bash
copy .env.example .env
```

Generer la cle Laravel :

```bash
php artisan key:generate
```

Configurer la base de donnees dans `.env`, puis lancer les migrations :

```bash
php artisan migrate
```

Demarrer le serveur :

```bash
php artisan serve
```

Adresse locale :

```text
http://127.0.0.1:8000
```

## Commandes utiles

Vider les caches :

```bash
php artisan optimize:clear
```

Lancer les tests :

```bash
php artisan test
```

Verifier les routes :

```bash
php artisan route:list
```

## Notes importantes pour la presentation

- Demarrer MySQL avant de lancer les migrations ou l'application.
- Verifier les informations `.env` : base de donnees et configuration mail.
- Si l'envoi d'email est mal configure en local, l'entretien est quand meme cree et l'erreur est enregistree dans les logs.
- Pour tester la 2FA par application, scanner le QR Code depuis les parametres du compte.
- Pour changer la langue, aller dans les parametres puis choisir `Francais` ou `English`.

## Structure rapide du projet

- `app/Http/Controllers` : logique principale des pages.
- `app/Models` : modeles Eloquent lies aux tables.
- `app/Mail` : emails envoyes par StageLink.
- `app/Services` : services reutilisables comme la 2FA TOTP.
- `resources/views` : interfaces Blade.
- `resources/lang` : traductions francais / anglais.
- `public/js` : scripts JavaScript des dashboards.
- `public/css` : styles des dashboards.
- `database/migrations` : creation et modification des tables.

## Auteur

Projet realise dans le cadre du developpement de StageLink.
