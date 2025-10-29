<?php
    /* Template Name: Página de Register */

    if (is_user_logged_in()) {
        wp_redirect(home_url('/painel'));
        exit;
    }

    if (isset($_POST['register'])) {

        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        if (username_exists($username) || email_exists($email)) {
            $erro = 'O nome de usuário ou e-mail já está em uso.';
        } else {
            $user_id = wp_create_user($username, $password, $email);

            if (is_wp_error($user_id)) {
                $erro = 'Erro ao criar o usuário.';
            } else {
                $user = new WP_User($user_id);
                $user->set_role('student');

                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);

                wp_redirect(home_url('/painel'));
                exit;
            }
        }
    }

    wp_head();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Public+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class='div-geral'>
        <div class='banner-left' style="background-image: url('<?php echo get_option('banner_principal'); ?>');"></div>
        <div class="div-right">
            <div class='div-logovertical'>
                <img src="<?php echo get_option('logo_site_colorida'); ?>" alt="Logo" class='logo-login-vertical'/>
            </div>
            <div class='div-form'>
                <form method="post" action="">
                    <h1 class="titulo">Criar Conta</h1>
                    <br/><br/>
                    <label class='input-label'>Nome</label>
                    <br/>
                    <input name='username' class='input' type='text' placeholder='Usuário' 
                    value=''
                    required
                    />
                    <br/>
                    <label class='input-label'>Email</label>
                    <br/>
                    <input name='email' class='input' type='text' placeholder='E-mail' 
                    value=''
                    required
                    />
                    <br/>
                    <label class='input-label'>Senha</label>
                    <br/>
                    <input name='password' class='input' type='password' placeholder='Senha'
                    value=''
                    required
                    />
                    <?php if (isset($erro) && !empty($erro)): ?>
                        <p class="erro"><?php echo esc_html($erro); ?></p>
                    <?php endif; ?>
                    <br/>
                    <button type='submit' class='button' name='register'>Cadastrar</button>
                    <p class='ppossuiumaconta'>Já possui uma conta?  <a class='linkpossuiconta' href="<?php echo site_url('/login'); ?>">Acesse aqui</a></p>
                </form>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>