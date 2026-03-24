# Blog CMS

Un CMS blog completo realizzato con **Symfony 6.4**, **PHP 8.2**, **MySQL** e **Tailwind CSS v4**.

Progetto dimostrativo che implementa autenticazione, pannello admin con gestione completa di articoli, categorie, tag, utenti e commenti, frontend pubblico con paginazione, filtri, sistema di commenti e reazioni (like/dislike).

---

## Tech Stack

- **Backend:** PHP 8.2, Symfony 6.4, Doctrine ORM
- **Database:** MySQL 8.0
- **Frontend:** Twig, Tailwind CSS v4, Webpack Encore
- **Paginazione:** KnpPaginatorBundle
- **Autenticazione:** Symfony Security (form login, remember me)

---

## Requisiti

- PHP >= 8.2
- Composer
- Node.js >= 18 e npm
- MySQL >= 8.0
- Symfony CLI (consigliato)

---

## Installazione

### 1. Clona il repository

```bash
git clone https://github.com/MichaelAlesiano/blog-cms.git
cd blog-cms
```

### 2. Installa le dipendenze

```bash
composer install
npm install
```

### 3. Configura l'ambiente

Crea il file `.env.local` partendo da `.env`:

```bash
cp .env .env.local
```

Apri `.env.local` con un editor di testo e aggiorna `DATABASE_URL` con le tue credenziali MySQL:

```
DATABASE_URL="mysql://utente:password@127.0.0.1:3306/blog_cms?serverVersion=8.0.32&charset=utf8mb4"
```

### 4. Crea il database e carica i dati di esempio

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

### 5. Compila gli asset e avvia il server

Apri due terminali nella cartella del progetto.

Nel primo, avvia la compilazione degli asset:

```bash
npm run watch
```

Nel secondo, avvia il server Symfony:

```bash
symfony server:start
```

L'applicazione sarà disponibile all'indirizzo mostrato nel terminale (di solito `https://127.0.0.1:8000`).

---

## Accesso al pannello admin

Dopo aver caricato le fixtures:

- **URL:** `/login`
- **Email:** `admin@blog-cms.dev`
- **Password:** `admin`

---

## Struttura del progetto

```
blog-cms/
├── assets/
│   └── styles/
│       └── app.css             # Tailwind CSS + custom styles
├── config/                     # Configurazione Symfony
├── migrations/                 # Migrazioni Doctrine
├── public/
│   └── uploads/covers/         # Upload immagini di copertina
├── src/
│   ├── Controller/
│   │   ├── Admin/              # ArticleController, CategoryController,
│   │   │                       # TagController, UserController,
│   │   │                       # CommentController, DashboardController
│   │   ├── BlogController      # Frontend pubblico + like/dislike
│   │   └── SecurityController
│   ├── Entity/                 # Article, Category, Tag, User, Comment
│   ├── Form/                   # ArticleType, CategoryType, TagType,
│   │                           # UserType, CommentType
│   ├── Repository/             # Repository Doctrine con query custom
│   └── DataFixtures/           # Fixtures con dati di esempio
├── templates/
│   ├── admin/                  # Pannello admin con sidebar
│   ├── blog/                   # Frontend pubblico
│   └── security/               # Login
├── postcss.config.mjs
├── webpack.config.js
├── .env
└── README.md
```

---

## Funzionalità

### Frontend pubblico
- Homepage con lista articoli paginata
- Pagina dettaglio articolo
- Filtro per categoria con sidebar
- Sistema di commenti (nome, email opzionale, testo)
- Reazioni like/dislike via AJAX
- Contatore visualizzazioni

### Pannello admin
- Dashboard con statistiche e metriche di engagement (views, likes, commenti)
- CRUD completo articoli con anteprima bozza
- CRUD categorie con slug personalizzabile
- CRUD tag
- CRUD utenti con gestione ruoli (Admin/Utente)
- Moderazione commenti (lista e eliminazione)
- Sidebar di navigazione

### Tecnico
- Autenticazione con Symfony Security (form login, remember me, CSRF)
- Protezione area admin con ruoli
- Upload immagini di copertina con validazione
- Slug generati automaticamente (articoli, tag) o personalizzabili (categorie)
- Relazioni Doctrine: ManyToOne, ManyToMany, OneToMany
- Like/dislike via endpoint JSON (AJAX)
- Design responsive con Tailwind CSS v4

---

## Comandi utili

```bash
# Compila asset in sviluppo (con watch)
npm run watch

# Compila asset per produzione
npm run build

# Pulisci la cache Symfony
php bin/console cache:clear

# Ricrea il database da zero
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

---

## Licenza

Progetto dimostrativo — uso personale.
