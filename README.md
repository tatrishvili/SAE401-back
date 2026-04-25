# Eco-App — Backend (Symfony 7.4)

API REST pour l'application Eco-App (suivi d'empreinte carbone).
Le frontend Vue.js consomme cette API via HTTP + JWT.

---

## Prérequis

- **PHP 8.2+** avec extensions : `pdo_sqlite`, `mbstring`, `xml`, `intl`, `curl`, `zip`
- **Composer**
- **OpenSSL** (pour générer les clés JWT)

> Sur Ubuntu/Debian :
> ```bash
> sudo apt install php-cli php-xml php-mbstring php-curl php-intl php-sqlite3 php-zip composer
> ```

---

## Installation rapide (à partir d'un clone frais)

### 1. Installer les dépendances PHP

```bash
composer install
```

### 2. Créer les fichiers d'environnement locaux

> ⚠️ `.env.local` n'est **pas** versionné (gitignoré). À recréer après chaque clone.

Crée `.env.local` à la racine :

```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

> Le fichier `.env` (versionné) contient déjà des défauts identiques. `.env.local` permet de surcharger en local sans toucher au commit.

### 3. Générer les clés JWT

> ⚠️ Les clés JWT sont **gitignorées** (`config/jwt/*.pem`). À regénérer après chaque clone.

```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem 4096
openssl rsa -in config/jwt/private.pem -pubout -out config/jwt/public.pem
```

### 4. Créer la base SQLite + charger les données

```bash
mkdir -p var
touch var/data.db
php bin/console doctrine:schema:create --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

Cela crée la base `var/data.db` (gitignorée) et y insère :
- 30 paliers (steps)
- 60 défis (challenges)
- 3 badges
- 17 conseils écologiques (tips)

### 5. Démarrer le serveur

```bash
php -S localhost:8000 -t public
# OU avec la CLI Symfony :
symfony server:start
```

L'API est disponible sur **http://localhost:8000/api**.

---

## Vérification rapide

```bash
curl http://localhost:8000/api/tips        # 17 conseils
curl http://localhost:8000/api/challenges  # 60 défis
```

---

## Endpoints

### Publics (sans JWT)
| Méthode | Route | Description |
|---------|-------|-------------|
| POST | `/api/register` | Crée un compte (`{name, email, password}`) |
| POST | `/api/login` | Retourne un JWT (`{email, password}`) |
| GET  | `/api/categories` | Catégories CO2 (proxy ImpactCo2) |
| GET  | `/api/transport?km=&transports=` | CO2 transport |
| GET  | `/api/food?category=group` | CO2 alimentation |
| GET  | `/api/fruitsetlegumes?month=` | Fruits/légumes saison |
| GET  | `/api/tips` | Conseils écologiques |
| GET  | `/api/challenges` | Liste des défis |
| GET  | `/api/steps` | Liste des paliers |

### Authentifiés (header `Authorization: Bearer <token>`)
| Méthode | Route | Description |
|---------|-------|-------------|
| GET    | `/api/me` | Profil utilisateur |
| GET    | `/api/me/stats` | XP + badges débloqués |
| GET    | `/api/entries` | Entrées CO2 du user |
| POST   | `/api/entries` | Créer une entrée |
| PUT    | `/api/entries/{id}` | Modifier |
| DELETE | `/api/entries/{id}` | Supprimer |
| GET    | `/api/entries/today` | Entrées du jour |
| GET    | `/api/steps/{id}/challenges` | Défis du palier |
| POST   | `/api/steps/{id}/unlock-next` | Valider et débloquer le suivant |
| POST   | `/api/debug/add-xp` | (Debug) Ajouter de l'XP au user |

---

## Structure

```
src/
├── Entity/        # Entités Doctrine (User, DailyEntry, Challenge, Step, Badge, Tip, UserChallenge)
├── Controller/    # Contrôleurs API (Auth, Api, DailyEntry, Challenge, Step, UserStats, Tip)
├── Repository/    # Requêtes BDD
├── Service/       # Logique métier (GamificationService, ImpactCo2ApiService)
├── Security/      # Voters
└── DataFixtures/  # Seed data
config/packages/   # Config Symfony (security, doctrine, cors, jwt, nelmio_cors)
config/jwt/        # Clés JWT (gitignorées)
migrations/        # Migrations Doctrine (vide en SQLite, on utilise schema:create)
var/               # Cache + base SQLite (gitignoré)
```

---

## Reset complet de la base

```bash
rm var/data.db && touch var/data.db
php bin/console doctrine:schema:create --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

---

## Fichiers gitignorés (à recréer en local)

- `.env.local` — overrides d'environnement local
- `config/jwt/*.pem` — clés JWT (à regénérer avec openssl)
- `var/` — cache Symfony + base SQLite
- `vendor/` — dépendances Composer
