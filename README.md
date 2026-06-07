# WordPress Course Platform Project - Theme + Plugin

This repository contains a **custom theme** and a **custom plugin** developed for a WordPress course platform.
The project was created with the goal of deepening knowledge in WordPress development, integrating a custom layout, access control, and payment integration via Stripe.

## Structure

* `/wp-content/themes/plataforma-cursos-theme` — custom theme
* `/wp-content/plugins/comprarcurso` — auxiliary plugin responsible for checkout and access control

## Installation

1. Copy the `plataforma-cursos-theme` theme folder to the `wp-content/themes/` directory of your WordPress installation.
2. Copy the `comprarcurso` plugin folder to the `wp-content/plugins/` directory of your WordPress installation.
3. The plugin uses the Stripe API to process payments.

### Installation

1. Go to the plugin folder `/wp-content/plugins/comprarcurso` and install the dependencies using Composer:
   composer install

2. Add your Stripe keys to the `wp-config.php` file:
   define( 'STRIPE_SECRET_KEY', 'your_secret_key_here' );
   define( 'STRIPE_PUBLIC_KEY', 'your_public_key_here' );

3. Activate the theme in **Appearance -> Themes**.

<img width="742" height="499" alt="image" src="https://github.com/user-attachments/assets/0a3bc00e-57b2-402e-996f-df45d388c3d0" />

6. Activate the plugin in **Plugins -> Installed Plugins**.

<img width="1363" height="417" alt="image" src="https://github.com/user-attachments/assets/c41a636b-99cf-4004-b6ee-63545c5efcee" />

## Features

* Theme: modern layout, custom fields, and menu support.
* Plugin: user access control for courses and checkout integration.

© 2025 — Study and practice project in WordPress 6.8.3 development.
