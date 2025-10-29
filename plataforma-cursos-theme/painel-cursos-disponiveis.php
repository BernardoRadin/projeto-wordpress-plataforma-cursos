<?php 
    /*
        Template Name: Painel Cursos Disponíveis
    */

    get_header();

    $args = array(
        'post_type' => 'curso',
        'posts_per_page' => -1
    );

?>
        <div style="padding: 10px 40px 10px 40px">
            <div class="header-div-card">
                <h1 class="titulo-pagina">Cursos Disponíveis</h1>
                <div class="align-inputs">
                    <input type="text" class="input-search" placeholder="Pesquisar por palavras-chaves ou cursos"/>
                    <button class="button-search"> <i data-lucide="search"></i> Buscar</button>
                </div>
            </div>
            <?php
                $loop = new WP_Query($args);

                if ($loop->have_posts()) : ?>
                <div class="div-cards">
                    <?php while ($loop->have_posts()) : $loop->the_post();

                        if (has_post_thumbnail()) {
                            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                        } else {
                            $thumbnail = get_template_directory_uri() . '/assets/images/sem-thumb.png';
                        }

                        $descricao = get_post_meta(get_the_ID(), '_descricao_curso', true);
                    ?>
                        <div class="card">
                            <div class="card-image" style='background-image: linear-gradient(rgba(0, 0, 0, 0.25) 50%, rgba(0, 0, 0, 0.6) 100%), url(<?php echo $thumbnail ?>);'></div>
                            <div class="info">
                                <h4 class="titulo-card"><?php the_title(); ?></h4>
                                <p class="descricao"><?php echo esc_attr($descricao); ?></p>
                                <a class="conheca-curso" href='<?php the_permalink(); ?>'>Conheça o curso <i data-lucide="arrow-right"></i></a>
                            </div>
                        </div>
                    <?php

                    endwhile;
                    ?>
                </div>
            <?php
                else:
                    echo '<p>Nenhum curso encontrado.</p>';
                endif;
            ?>
        </div>
<?php get_footer(); ?>