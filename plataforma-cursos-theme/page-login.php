<?php
    /* Template Name: Página de Login */

    if (is_user_logged_in()) {
        wp_redirect(home_url('/painel'));
        exit;
    }

    if (isset($_POST['login'])) {
        $creds = array(
            'user_login'    => sanitize_user($_POST['username']),
            'user_password' => $_POST['password'],
            'remember'      => true
        );

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            $erro = 'Usuário ou senha incorretos.';
        } else {
            wp_redirect(home_url('/painel'));
            exit;
        }
    }

    wp_head();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Public+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class='div-geral'>
        <div class='banner-left' style="background-image: url('<?php echo get_option('banner_principal'); ?>');">
        </div>
        <div class="div-right">
            <div class='div-logovertical'>
                <img src="<?php echo get_option('logo_site_colorida'); ?>" alt="Logo" class='logo-login-vertical'/>
            </div>
            <div class='div-form'>
                <form method="post" action="">
                    <h1 class="titulo">Acesse sua conta</h1>
                    <br/><br/>
                    <label class='input-label'>Email</label>
                    <br/>
                    <input name='username' class='input' type='text' placeholder='Usuário ou e-mail' required/>
                    <br/>
                    <label class='input-label'>Senha</label>
                    <br/>
                    <input name='password' class='input' type='password' placeholder='Senha' required/>
                    <?php if (isset($erro) && !empty($erro)): ?>
                        <p class="erro"><?php echo esc_html($erro); ?></p>
                    <?php endif; ?>
                    <a class='esqueciminhasenha'>Esqueci minha senha</a>
                    <button type='submit' class='button' name='login'>Acessar</button>
                    <p class='ppossuiumaconta'>Não possui uma conta?  <a class='linkpossuiconta' href="<?php echo site_url('/register'); ?>">Acesse aqui</a></p>
                </form>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>