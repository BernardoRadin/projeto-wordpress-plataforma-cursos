<?php
    if (!is_user_logged_in()) {
        wp_redirect(home_url('/login'));
        exit;
    }

    wp_head();

    $current_url = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Public+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="menu-lateral" id="menuLateral">
        <span class="close-btn" id="closeMenu">&times;</span>
        <img src="<?php echo get_option('logo_site_branca'); ?>" width="100"/>
        <ul class='ul-menu'>
            <a style='text-decoration: none' href='<?php echo site_url('/'); ?>'><li class="meus-cursos  <?php echo $current_url =='/plataformacursos/' ? 'selected' : ''; ?>"> <i data-lucide="House"></i> <span id="text-meus-cursos"> Meus Cursos </span></li></a>
            <a style='text-decoration: none' href='<?php echo site_url('/cursos-disponiveis'); ?>'><li class="cursos <?php echo (strpos($current_url, '/cursos-disponiveis/') !== false || strpos($current_url, '/curso/') !== false) ? 'selected' : ''; ?>"> <i data-lucide="square-play"></i> <span id="text-cursos"> Cursos </span></li></a>
        </ul>
    </div>

    <div class="container" id="container">
        <div class="menu-topo">
            <div class="hamburguer" id="toggleMenu">&#9776;</div>
            <div class='div-align-user'>
                <i class='user' data-lucide="circle-user-round"></i>
                <i class='narrow' data-lucide="chevron-down" width='13'></i>
            </div>
        </div>
        <div class='user-menu'>
            <ul>
                <li><i data-lucide="user"></i> Meu Perfil</li>
                <a href="<?php echo wp_logout_url( site_url('/login') ); ?>"><li><i data-lucide="door-open"></i> Sair</li></a>
            </ul>
        </div>