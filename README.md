# Eco-App

## Overview

Eco-App is a carbon footprint tracking application built with Symfony 7.4 (backend) and Vue.js (frontend).  
The backend handles all logic, API calls, and database storage. The frontend communicates via HTTP requests and displays dashboards, forms, and graphs.

---

## Prerequisites

- PHP 8.2+  
- Composer  
- MySQL / PostgreSQL  
- Node.js & npm (for Vue.js frontend)  

---

## Setup Instructions

### 1. Install PHP dependencies

```bash
composer install
composer require asset-mapper
composer require symfony/orm-pack
composer require --dev symfony/maker-bundle
composer require doctrine/doctrine-migrations-bundle
composer require symfony/serializer-pack
composer require nelmio/cors-bundle
