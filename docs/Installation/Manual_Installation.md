# NexaAIOne Manual Installation Guide

## Introduction

This guide provides step-by-step instructions on how to manually deploy NexaAIOne. Although this guide focuses on manual installation, we also offer a [Docker container](https://github.com/mrahmadt/NexaAIOne/blob/main/docs/Installation/docker.md) for faster and simpler deployment. [The Docker method is recommended for most use cases](https://github.com/mrahmadt/NexaAIOne/blob/main/docs/Installation/docker.md).

## Prerequisites

Ensure the following software is installed:

- PHP 8.2 minimum
- Redis Server
- Postgres
- Pgvector ([Github Repository](https://github.com/pgvector/pgvector))
- NGINX or Apache web server

## Step 1: Clone NexaAIOne Repository

First, clone the NexaAIOne repository to your desired directory:

```bash
git clone https://github.com/mrahmadt/NexaAIOne.git
```

## Step 2: Change Directory

Navigate into the cloned directory:

```bash
cd NexaAIOne
```

## Step 3: Create Configuration File

Make a copy of the example configuration file:

```bash
cp .env.example .env
```

## Step 4: Edit Configuration File

Open the `.env` file and modify it as necessary to suit your environment. Follow the inline comments for guidance.

## Step 5: Laravel Specific Configuration

For additional settings and optimization, refer to the Laravel deployment documentation: [Laravel Deployment Guide](https://laravel.com/docs/10.x/deployment)

## Step 6: Run Necessary Commands

Run the following commands to set up and optimize your Laravel application:

```bash
su www-data -s /bin/bash -c "composer install --optimize-autoloader --no-dev"
su www-data -s /bin/bash -c "php artisan key:generate"
su www-data -s /bin/bash -c "php artisan config:cache"
su www-data -s /bin/bash -c "php artisan route:cache"
su www-data -s /bin/bash -c "php artisan view:cache"
su www-data -s /bin/bash -c "php artisan storage:link"
su www-data -s /bin/bash -c "php artisan optimize"
su www-data -s /bin/bash -c "php artisan horizon:publish"
```

## Step 7: Create Database and Seed Data

Run the following command to create the database tables and seed them:

```bash
php artisan migrate --seed --force
```

## Step 8: Create Admin User

Create an admin user for the admin portal:

```bash
php artisan make:filament-user
```

## Step 9: Access Admin Portal

You can now log in to the Admin portal by navigating to [https://localhost/admin](https://localhost/admin).

---

Congratulations, you've successfully deployed NexaAIOne manually! If you encounter any issues, feel free to open an ticket https://github.com/mrahmadt/NexaAIOne/issues
