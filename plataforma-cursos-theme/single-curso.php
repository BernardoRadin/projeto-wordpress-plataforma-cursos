<?php 

    get_header();

    global $wpdb;
    $current_user_id = get_current_user_id();
    $curso_id = get_the_ID();

    $table_name = $wpdb->prefix . 'comprarcurso_acessos';
    $tem_acesso = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND curso_id = %d AND status = %s",
            $current_user_id,
            $curso_id,
            'ativo'
        )
    );

    $thumbnail = has_post_thumbnail() ? get_the_post_thumbnail_url($curso_id,'full') : get_template_directory_uri().'/assets/images/sem-thumb.png';
    $descricao = get_post_meta($curso_id, '_descricao_curso', true);
    $preco     = get_post_meta($curso_id, '_preco_curso', true);
    $modulos   = get_post_meta($curso_id, '_modulos_curso', true);

    if (!is_array($modulos)){
        $modulos = [];
    }
?>
        <div style="padding: 10px 40px 10px 40px">
            <div class="header-div-card">
                <h1 class="titulo-pagina"><?php the_title();?></h1>
            </div>
            <?php if (!$tem_acesso) : ?>
                <div class="curso-banner">
                    <img src="<?php echo esc_html($thumbnail); ?>" alt="Curso de Web Design Express">
                </div>
                <div class="curso-info">
                    <h3>Descrição</h3>
                    <p class="descricao-curso">
                        <?php echo esc_html($descricao) ?>
                    </p>
                </div>
                <div class="modulos-publicos">
                    <h3>Conteúdo do Curso</h3>
                                
                    <?php foreach ($modulos as $i => $modulo): ?>
                        <div class="modulo">
                            <h4><?php echo esc_html($modulo['titulo']); ?></h4>
                            <?php if (isset($modulo['videos']) && is_array($modulo['videos']) && count($modulo['videos']) > 0): ?>
                                <ul>
                                    <?php foreach ($modulo['videos'] as $video): ?>
                                        <li><?php echo esc_html($video['nome'] ?? $video); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Sem vídeos cadastrados.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?php echo site_url('/matricular?curso=' . $post->post_name); ?>" class="btn-matricular">Matricule-se agora (R$ <?php echo esc_html($preco) ?>)</a>
            <?php else: ?>
                            
                <div class="container-aula">
                    <div class="video-section">
                        <?php
                            $modulo_inicial = reset($modulos);
                            $video_inicial = !empty($modulo_inicial['videos'][0]) ? $modulo_inicial['videos'][0] : null;
                        ?>
                        <div class="video-wrapper">
                            <?php if ($video_inicial): ?>
                                <video id="video-player" controls controlsList="nodownload" disablePictureInPicture poster="<?php echo esc_url($thumbnail); ?>">
                                    <source src="<?php echo site_url('wp-content/themes/plataforma-cursos/stream-video.php?curso_id=' . $curso_id .'&video=' . urlencode($video_inicial['arquivo'])); ?>" type="video/mp4">
                                    Seu navegador não suporta vídeo HTML5.
                                </video>
                            <?php else: ?>
                                <p>Nenhum vídeo disponível.</p>
                            <?php endif; ?>
                        </div>
                        <div class="aula-info">
                            <h2 class="titulo-aula"><?php echo $video_inicial ? esc_html($video_inicial['nome']) : ''; ?></h2>
                            <p class="descricao-aula">
                               <?php echo $video_inicial['descricao'] ? esc_html($video_inicial['descricao']) : 'Vídeo aula sem descrição.'; ?>
                            </p>
                        </div>
                    </div>
                    <aside class="sidebar-modulos">
                        <?php foreach ($modulos as $m_index => $modulo) : ?>
                        <div class="modulo <?php echo $m_index === 0 ? 'ativo' : ''; ?>">
                            <div class="modulo-header">
                                <h4 class="modulo-titulo"><?php echo esc_html($modulo['titulo']); ?></h4>
                                <i class="icone-toggle" data-lucide="chevron-down"></i>
                            </div>
                            <ul class="lista-aulas">
                                <?php foreach ($modulo['videos'] as $v_index => $video) : ?>
                                <li class="aula-item <?php echo ($m_index === 0 && $v_index === 0) ? 'ativa' : ''; ?>"
                                    data-video="<?php echo esc_url($video['arquivo']); ?>"
                                    data-titulo="<?php echo esc_attr($video['nome']); ?>"
                                    data-descricao="<?php echo esc_attr($video['descricao']); ?>"
                                    data-curso="<?php echo esc_attr($curso_id); ?>">
                                    <?php echo esc_html($video['nome']); ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endforeach; ?>

                    </aside>
            </div>

            <script>
                jQuery(document).ready(function($){
                    $('.aula-item').click(function(){
                        var videoURL = $(this).data('video');
                        var titulo = $(this).data('titulo');
                        var curso = $(this).data('curso');
                        var descricao = $(this).data('descricao');

                        $('#video-player source').attr(
                        'src',
                        '<?php echo site_url("wp-content/themes/plataforma-cursos/stream-video.php"); ?>'
                        + '?curso_id=' + curso
                        + '&video=' + encodeURIComponent(videoURL)
                        );
                        $('#video-player')[0].load();
                        $('.titulo-aula').text(titulo);
                        $('.descricao-aula').text(descricao);

                        $('.aula-item').removeClass('ativa');
                        $(this).addClass('ativa');
                    });

                $('.modulo-header').on('click', function () {
                    let modulo = $(this).closest('.modulo');
                        
                    modulo.toggleClass('ativo');
                });
                });
            </script>
            <?php endif; ?>
    </div>
<?php get_footer(); ?>