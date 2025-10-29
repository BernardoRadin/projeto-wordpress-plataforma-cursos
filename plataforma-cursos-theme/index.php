<?php 
 
get_header();

$current_user_id = get_current_user_id();

$table_name = $wpdb->prefix . 'comprarcurso_acessos';
$curso_ids = $wpdb->get_col(
    $wpdb->prepare(
        "SELECT curso_id FROM $table_name WHERE user_id = %d AND status = %s",
        $current_user_id,
        'ativo'
    )
);

if ( empty($curso_ids) ) {
    $cursos = [];
} else {
    $args = [
        'post_type'      => 'curso',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post__in'       => $curso_ids
    ];

    $cursos = new WP_Query($args);
}

?>

<div style="padding: 10px 40px 10px 40px">
    <div class="header-div-card">
        <h1 class="titulo-pagina">Meus Cursos</h1>
        <div class="align-inputs">
            <input type="text" class="input-search" placeholder="Pesquisar por palavras-chaves ou cursos"/>
            <button class="button-search"> <i data-lucide="search"></i> Buscar</button>
        </div>
    </div>
        <div class="div-cards">
            <?php if (!empty($cursos)) : ?>
                <?php while ($cursos->have_posts()) : $cursos->the_post(); ?>
                    <?php 
                        $thumbnail = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'medium') : get_template_directory_uri() . '/assets/images/sem-thumb.png';
                        $descricao = get_post_meta(get_the_ID(), '_descricao_curso', true);
                    ?>
                    <div class="card">
                        <div class="card-image" style='background-image: linear-gradient(rgba(0, 0, 0, 0.25) 50%, rgba(0, 0, 0, 0.6) 100%), url(<?php echo $thumbnail ?>);'></div>
                        <div class="info">
                            <h4 class="titulo-card"><?php the_title(); ?></h4>
                            <a href="<?php echo site_url('/campus/' . $post->post_name); ?>" class='acessar-curso'>Acessar Curso <i data-lucide="play"></i></a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p>Nenhum curso encontrado.</p>
                <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>
