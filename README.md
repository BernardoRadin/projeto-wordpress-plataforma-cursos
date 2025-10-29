# Projeto WordPress Plataforma Cursos - Tema + Plugin

Este repositório contém um **tema** e um **plugin personalizados** desenvolvidos para uma plataforma de cursos em WordPress.  
O projeto foi criado com o objetivo de aprofundar os conhecimentos em desenvolvimento WordPress, integrando layout customizado, controle de acesso e integração com pagamentos via Stripe.

## Estrutura
- `/wp-content/themes/plataforma-cursos-theme` — tema personalizado
- `/wp-content/plugins/comprarcurso` — plugin auxiliar responsável pelo checkout e controle de acesso

## Instalação
1. Copie a pasta do tema `plataforma-cursos-theme` para a pasta `wp-content/themes/` do seu WordPress.
2. Copie a pasta do plugin `comprarcurso` para a pasta `wp-content/plugins/` do seu WordPress.
3. O plugin utiliza API da Stripe para processar pagamentos.

### Instalação
1. Acesse a pasta do plugin `/wp-content/plugins/comprarcurso` e instale as dependências com o Composer:
    composer install
2. Adicione suas chaves da Stripe no arquivo `wp-config.php` 
    define( 'STRIPE_SECRET_KEY', 'sua_chave_secreta_aqui' );
    define( 'STRIPE_PUBLIC_KEY', 'sua_chave_publica_aqui' );

4. Ative o tema em **Aparência -> Temas**.
   
<img width="742" height="499" alt="image" src="https://github.com/user-attachments/assets/0a3bc00e-57b2-402e-996f-df45d388c3d0" />

6. Ative o plugin em **Plugins -> Instalados**.

<img width="1363" height="417" alt="image" src="https://github.com/user-attachments/assets/c41a636b-99cf-4004-b6ee-63545c5efcee" />

## 💡 Funcionalidades
- Tema: layout moderno, custom fields e suporte a menus.
- Plugin: controle de acesso dos usuários aos cursos e integração com checkout.

© 2025 — Projeto de estudo e prática em desenvolvimento WordPress.
