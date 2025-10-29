<?php

// MENU CADASTRO DE CURSOS
function cursos_register_post_type() {
    $args = array(
            'labels' => array(
            'name'               => 'Cursos',
            'singular_name'      => 'Curso',
            'menu_name'          => 'Cursos',
            'name_admin_bar'     => 'Curso',
            'add_new'            => 'Adicionar Curso',
            'add_new_item'       => 'Adicionar Novo Curso',
            'new_item'           => 'Novo Curso',
            'edit_item'          => 'Editar Curso',
            'view_item'          => 'Ver Curso',
            'all_items'          => 'Todos os Cursos',
            'search_items'       => 'Buscar Cursos',
            'not_found'          => 'Nenhum curso encontrado',
            'not_found_in_trash' => 'Nenhum curso encontrado na lixeira'
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => array('title', 'thumbnail'),
        'rewrite' => array('slug' => 'curso'),
        'show_in_rest' => false,
    );
    register_post_type('curso', $args);
}

// ADICIONA META BOX    
function cursos_add_custom_meta_box() {
    add_meta_box(
        'cursos_meta_box',
        'Informações do Curso',
        'cursos_render_meta_box',
        'curso',
        'normal',
        'high'
    );
}

function cursos_hide_publish_sidebar() {
    echo '<style>
        #submitdiv { display: none !important; }
    </style>';
}

add_action('init', 'cursos_register_post_type');
add_action('add_meta_boxes_curso', 'cursos_add_custom_meta_box');
add_theme_support('post-thumbnails');
add_action('admin_head-post.php', 'cursos_hide_publish_sidebar');
add_action('admin_head-post-new.php', 'cursos_hide_publish_sidebar');
add_action('post_edit_form_tag', function() {
    echo ' enctype="multipart/form-data"';
});
update_option('uploads_use_yearmonth_folders', 0);

// RENDERIZA CAMPOS
function cursos_render_meta_box($post) {
    $descricao   = get_post_meta($post->ID, '_descricao_curso', true);
    $preco   = get_post_meta($post->ID, '_preco_curso', true);
    $duracao = get_post_meta($post->ID, '_duracao_curso', true);
    $nivel   = get_post_meta($post->ID, '_nivel_curso', true);
    $modulos = get_post_meta($post->ID, '_modulos_curso', true);
    if (!is_array($modulos)) $modulos = [];

    wp_nonce_field('cursos_save_meta_box', 'cursos_meta_box_nonce');
    
    ?>
    <p><label>Descrição:</label><br>
        <input type="text" name="descricao_curso" value="<?php echo esc_attr($descricao); ?>" style="width:100%">
    </p>
    <p><label>Preço (R$):</label><br>
        <input type="text" name="preco_curso" value="<?php echo esc_attr($preco); ?>" style="width:100%">
    </p>
    <p><label>Duração (horas):</label><br>
        <input type="text" name="duracao_curso" value="<?php echo esc_attr($duracao); ?>" style="width:100%">
    </p>
    <p><label>Nível:</label><br>
        <select name="nivel_curso" style="width:100%">
            <option value="">Selecione...</option>
            <option value="iniciante" <?php selected($nivel, 'iniciante'); ?>>Iniciante</option>
            <option value="intermediario" <?php selected($nivel, 'intermediario'); ?>>Intermediário</option>
            <option value="avancado" <?php selected($nivel, 'avancado'); ?>>Avançado</option>
        </select>
    </p>
    <div id="modulos-container">
        <?php foreach ($modulos as $i => $modulo): ?>
            <div class="modulo-item" style="border:1px solid #ddd;padding:10px;margin-bottom:15px;">
                <p><label>Título do Módulo:</label><br>
                    <input type="text" name="modulos[<?php echo $i; ?>][titulo]" value="<?php echo esc_attr($modulo['titulo'] ?? ''); ?>" style="width:100%">
                </p>

                <div class="videos-container">
                    <?php if (!empty($modulo['videos']) && is_array($modulo['videos'])): ?>
                        <?php foreach ($modulo['videos'] as $j => $video): ?>
                            <div class="video-item" style="margin-bottom:10px;border:1px dashed #ccc;padding:8px;">
                                <p><label>Nome do Vídeo:</label><br>
                                    <input type="text" name="modulos[<?php echo $i; ?>][videos][<?php echo $j; ?>][nome]" value="<?php echo esc_attr($video['nome'] ?? ''); ?>" style="width:100%" required>
                                </p>
                                <p><label>Descrição do Vídeo:</label><br>
                                    <input type="text" name="modulos[<?php echo $i; ?>][videos][<?php echo $j; ?>][descricao]" value="<?php echo esc_attr($video['descricao'] ?? ''); ?>" style="width:100%" required>
                                </p>
                                <p><label>Arquivo (MP4):</label><br>
                                    <input type="file" name="modulos[<?php echo $i; ?>][videos][<?php echo $j; ?>][arquivo]" accept="video/mp4">
                                    <?php if (!empty($video['arquivo'])): ?>
                                        <p><small>Atual: <?php echo esc_html(basename($video['arquivo'])); ?></small></p>
                                    <?php endif; ?>
                                </p>
                                <button type="button" class="button remove-video">Remover vídeo</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button type="button" class="button add-video" data-index="<?php echo $i; ?>" style="margin-top:10px;">Adicionar vídeo</button>
                <button type="button" class="button remove-modulo" style="margin-top:10px;">Remover módulo</button>
            </div>
        <?php endforeach; ?>
        <p>
            <button type="button" class="button" id="add-modulo">+ Adicionar Módulo</button>
        </p>
    </div>
    <p style="margin-top:20px;">
        <button type="button" class="button button-primary" onclick="document.getElementById('publish').click();">
            Publicar Curso
        </button>
    </p>

    <script>
    jQuery(document).ready(function($) {

        $('#add-modulo').on('click', function() {
            const container = $('#modulos-container');
            const index = container.children('.modulo-item').length;

            const modulo = $(`
                <div class="modulo-item" style="border:1px solid #ddd;padding:10px;margin-bottom:20px;">
                    <p><label><strong>Módulo:</strong></label><br>
                        <input type="text" name="modulos[${index}][titulo]" style="width:100%">
                    </p>

                    <div class="videos-container"></div>

                    <button type="button" class="button add-video" data-index="${index}" style="margin-top:10px;">Adicionar Vídeo</button>
                    <button type="button" class="button remove-modulo" style="margin-top:10px;margin-left:5px;">Remover Módulo</button>
                </div>
            `);

            container.append(modulo);
        });

        $(document).on('click', '.add-video', function() {
            const moduloIndex = $(this).data('index');
            const videosContainer = $(this).siblings('.videos-container');
            const videoIndex = videosContainer.children('.video-item').length;

            const video = $(`
                <div class="video-item" style="margin-top:10px;border:1px dashed #ccc;padding:10px;">
                    <p><label>Nome do Vídeo:</label><br>
                        <input type="text" name="modulos[${moduloIndex}][videos][${videoIndex}][nome]" style="width:100%" required>
                    </p>
                    <p><label>Descrição do Vídeo:</label><br>
                        <input type="text" name="modulos[${moduloIndex}][videos][${videoIndex}][descricao]" style="width:100%" required>
                    </p>
                    <p><label>Vídeo ${videoIndex + 1}:</label><br>
                        <input type="file" name="modulos[${moduloIndex}][videos][${videoIndex}][arquivo]" accept="video/mp4" style="width:100%">
                    </p>
                    <button type="button" class="button remove-video">Remover Vídeo</button>
                </div>
            `);

            videosContainer.append(video);
        });

        // Remover módulo
        $(document).on('click', '.remove-modulo', function() {
            $(this).closest('.modulo-item').remove();
        });

        // Remover vídeo
        $(document).on('click', '.remove-video', function() {
            $(this).closest('.video-item').remove();
        });

    });
    </script>

    <?php
}

function cursos_save_meta_box($post_id) {
    if (!isset($_POST['cursos_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['cursos_meta_box_nonce'], 'cursos_save_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['descricao_curso']))
        update_post_meta($post_id, '_descricao_curso', sanitize_text_field($_POST['descricao_curso']));
    if (isset($_POST['preco_curso']))
        update_post_meta($post_id, '_preco_curso', sanitize_text_field($_POST['preco_curso']));
    if (isset($_POST['duracao_curso']))
        update_post_meta($post_id, '_duracao_curso', sanitize_text_field($_POST['duracao_curso']));
    if (isset($_POST['nivel_curso']))
        update_post_meta($post_id, '_nivel_curso', sanitize_text_field($_POST['nivel_curso']));
    if (isset($_POST['modulos']) && is_array($_POST['modulos'])) {
        $modulos_sanitizados = [];
        $modulos_salvos = get_post_meta($post_id, '_modulos_curso', true) ?: [];

        foreach ($_POST['modulos'] as $modulo_index => $modulo) {
            $videos = [];

            if (isset($modulo['videos']) && is_array($modulo['videos'])) {
                
                foreach ($modulo['videos'] as $video_index => $video_data) {
                    $nome = sanitize_text_field($video_data['nome']);
                    $descricao = sanitize_text_field($video_data['descricao']);

                    $arquivo_url = $modulos_salvos[$modulo_index]['videos'][$video_index]['arquivo'] ?? '';

                    if (isset($_FILES['modulos']['name'][$modulo_index]['videos'][$video_index]['arquivo']) &&
                        !empty($_FILES['modulos']['name'][$modulo_index]['videos'][$video_index]['arquivo'])) {

                        $file = [
                            'name'     => $_FILES['modulos']['name'][$modulo_index]['videos'][$video_index]['arquivo'],
                            'type'     => $_FILES['modulos']['type'][$modulo_index]['videos'][$video_index]['arquivo'],
                            'tmp_name' => $_FILES['modulos']['tmp_name'][$modulo_index]['videos'][$video_index]['arquivo'],
                            'error'    => $_FILES['modulos']['error'][$modulo_index]['videos'][$video_index]['arquivo'],
                            'size'     => $_FILES['modulos']['size'][$modulo_index]['videos'][$video_index]['arquivo']
                        ];

                        require_once(ABSPATH . 'wp-admin/includes/file.php');

                        $curso_id = get_the_ID();

                        add_filter('upload_dir', function ($uploads) use ($curso_id) {
                            $uploads['subdir'] = '/cursos/' . $curso_id;
                            $uploads['path']   = $uploads['basedir'] . $uploads['subdir'];
                            $uploads['url']    = $uploads['baseurl'] . $uploads['subdir'];
                            return $uploads;
                        });

                        $upload = wp_handle_upload($file, ['test_form' => false]);

                        if (!isset($upload['error'])) {
                            $arquivo_url = esc_url_raw($upload['url']);
                        }
                    }

                    $videos[] = [
                        'nome'    => $nome,
                        'descricao' => $descricao,
                        'arquivo' => $arquivo_url
                    ];
                }
            }

            $modulos_sanitizados[] = [
                'titulo' => sanitize_text_field($modulo['titulo']),
                'videos' => $videos
            ];
        }

        update_post_meta($post_id, '_modulos_curso', $modulos_sanitizados);
    } else {
        delete_post_meta($post_id, '_modulos_curso');
    }
}
add_action('save_post_curso', 'cursos_save_meta_box');


//MENU CMS LOGO, BANNERS

add_action('admin_menu', 'meu_menu_configuracoes_site');
function meu_menu_configuracoes_site() {
    add_menu_page(
        'Configurações do Site',
        'Configurações do Site',
        'manage_options',
        'configuracoes-site',
        'pagina_configuracoes_site_html',
        'dashicons-admin-generic',
        25                         
    );
}

add_action('admin_enqueue_scripts', 'config_site_scripts');

function config_site_scripts($hook) {
    if ($hook != 'toplevel_page_configuracoes-site') {
        return;
    }
    wp_enqueue_media();
}

function pagina_configuracoes_site_html() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Salvar as opções
    if (isset($_POST['salvar_configuracoes'])) {
        update_option('logo_site_colorida', esc_url_raw($_POST['logo_site_colorida']));
        update_option('logo_site_branca', esc_url_raw($_POST['logo_site_branca']));
        update_option('banner_principal', esc_url_raw($_POST['banner_principal']));
        update_option('cor_primaria', sanitize_hex_color($_POST['cor_primaria']));
        update_option('cor_secundaria', sanitize_hex_color($_POST['cor_secundaria']));
        update_option('cor_hover', sanitize_hex_color($_POST['cor_hover']));
        update_option('menu_selected', sanitize_hex_color($_POST['menu_selected']));
        echo '<div class="updated"><p><strong>Configurações salvas!</strong></p></div>';
    }

    $logo_colorida = get_option('logo_site_colorida', '');
    $logo_branca = get_option('logo_site_branca', '');
    $banner = get_option('banner_principal', '');
    $cor_primaria = get_option('cor_primaria', '#FF7A00');
    $cor_secundaria = get_option('cor_secundaria', '#fa8a21');
    $cor_hover = get_option('cor_hover', '#FF8923');
    $menu_selected = get_option('menu_selected', '#ff8d26');

    ?>

    <div class="wrap">
        <h1>Configurações do Site</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="logo_site_colorida">Logo do Site (Colorida)</label></th>
                    <td>
                        <input type="text" name="logo_site_colorida" id="logo_site_colorida" value="<?php echo esc_attr($logo_colorida); ?>" size="70">
                        <button class="button upload-logo-colorida">Selecionar imagem</button>
                    </td>
                </tr>
                <tr>
                <th><label for="logo_site_branca">Logo do Site (Branca)</label></th>
                    <td>
                        <input type="text" name="logo_site_branca" id="logo_site_branca" value="<?php echo esc_attr($logo_branca); ?>" size="70">
                        <button class="button upload-logo-branca">Selecionar imagem</button>
                    </td>
                </tr>
                <tr>
                    <th><label for="banner_principal">Banner Principal Login</label></th>
                    <td>
                        <input type="text" name="banner_principal" id="banner_principal" value="<?php echo esc_attr($banner); ?>" size="70">
                        <button class="button upload-banner">Selecionar imagem</button>
                    </td>
                </tr>
                <tr>
                    <th><label for="cor_primaria">Cor Primária</label></th>
                    <td>
                        <input type="color" name="cor_primaria" id="cor_primaria" value="<?php echo esc_attr($cor_primaria); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="cor_secundaria">Cor Secundária</label></th>
                    <td>
                        <input type="color" name="cor_secundaria" id="cor_secundaria" value="<?php echo esc_attr($cor_secundaria); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="cor_hover">Cor Hover</label></th>
                    <td>
                        <input type="color" name="cor_hover" id="cor_hover" value="<?php echo esc_attr($cor_hover); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="menu_selected">Cor Menu(Painel) Selecionado</label></th>
                    <td>
                        <input type="color" name="menu_selected" id="menu-menu_selected" value="<?php echo esc_attr($menu_selected); ?>">
                    </td>
                </tr>
            </table>
            <?php submit_button('Salvar configurações', 'primary', 'salvar_configuracoes'); ?>
        </form>
    </div>

    <script>
        jQuery(document).ready(function($){
            function abrirUploader(inputId) {
                var frame = wp.media({
                    title: 'Selecionar imagem',
                    button: { text: 'Usar esta imagem' },
                    multiple: false
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#' + inputId).val(attachment.url);
                });

                frame.open();
            }

            $('.upload-logo-colorida').on('click', function(e) {
                e.preventDefault();
                abrirUploader('logo_site_colorida');
            });

            $('.upload-logo-branca').on('click', function(e) {
                e.preventDefault();
                abrirUploader('logo_site_branca');
            });

            $('.upload-banner').on('click', function(e) {
                e.preventDefault();
                abrirUploader('banner_principal');
            });
        });
    </script>
    <?php
}

// Retirar barra admin para não adms
add_filter('show_admin_bar', function($mostra){
    if (!current_user_can('manage_options')) return false;
    return $mostra;
});

// Redirecionar usuarios sem permissão
add_action('admin_init', function(){
    if (!current_user_can('manage_options') && !wp_doing_ajax()) {
        wp_redirect(home_url()); // ou /meus-cursos
        exit;
    }
});

//role estudante
add_role('student', 'Estudante', [
    'read' => true, 
    'edit_posts' => false,
]);

//Colocar cores personalizadas

add_action('wp_head', 'colocar_cores_personalizadas');

function colocar_cores_personalizadas() {
        $cor_primaria = get_option('cor_primaria', '#FF7A00');
        $cor_secundaria = get_option('cor_secundaria', '#fa8a21');
        $cor_hover = get_option('cor_hover', '#FF8923');
        $menu_selected = get_option('menu_selected', '#ff8d26');
    ?>
    <style>
        :root {
            --cor-primaria: <?php echo esc_html($cor_primaria); ?>;
            --cor-secundaria: <?php echo esc_html($cor_secundaria); ?>;
            --cor-hover: <?php echo esc_html($cor_hover); ?>;
            --menu-selected: <?php echo esc_html($menu_selected); ?>;
        }
    </style>
    <?php
}
