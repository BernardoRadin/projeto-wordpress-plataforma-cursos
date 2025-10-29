<?php
/**
 * Plugin Name: ComprarCurso - Sistema Compra de Cursos e Pagamentos
 * Description: Gerencia cursos, pedidos e controle de acesso seguro aos vídeos.
 * Version: 1.0
 * Author: Bernardo Radin
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/db.php';

register_activation_hook(__FILE__, 'comprarcurso_create_tables');

// /matricular
function comprarcurso_add_matricular_endpoint() {
    add_rewrite_rule(
        '^matricular/?',
        'index.php?comprarcurso_matricular=1',
        'top'
    );
}
add_action('init', 'comprarcurso_add_matricular_endpoint');

function comprarcurso_add_query_vars($vars) {
    $vars[] = 'comprarcurso_matricular';
    return $vars;
}
add_filter('query_vars', 'comprarcurso_add_query_vars');

function comprarcurso_template_redirect() {
    if (get_query_var('comprarcurso_matricular')) {
        include plugin_dir_path(__FILE__) . 'pages/matricular.php';
        exit;
    }
}
add_action('template_redirect', 'comprarcurso_template_redirect');


function comprarcurso_add_sucesso_endpoint() {
    add_rewrite_rule('^compra-concluida/?$', 'index.php?comprarcurso_sucesso=1', 'top');
}
add_action('init', 'comprarcurso_add_sucesso_endpoint');

function comprarcurso_add_cancelada_endpoint() {
    add_rewrite_rule('^compra-cancelada/?$', 'index.php?comprarcurso_cancelada=1', 'top');
}
add_action('init', 'comprarcurso_add_cancelada_endpoint');

function comprarcurso_add_query_vars_sucesso($vars) {
    $vars[] = 'comprarcurso_sucesso';
    $vars[] = 'comprarcurso_cancelada';
    return $vars;
}
add_filter('query_vars', 'comprarcurso_add_query_vars_sucesso');

function comprarcurso_template_redirect_sucesso() {
    if (get_query_var('comprarcurso_sucesso')) {
        include plugin_dir_path(__FILE__) . 'pages/compra-concluida.php';
        exit;
    }
    if (get_query_var('comprarcurso_cancelada')) {
        include plugin_dir_path(__FILE__) . 'pages/compra-cancelada.php';
        exit;
    }
}

add_action('template_redirect', 'comprarcurso_template_redirect_sucesso');

function user_has_purchased_course($user_id, $curso_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'comprarcurso_acessos';
    $result = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d AND curso_id = %d AND status = %s",
            $user_id, $curso_id, 'ativo'
        )
    );
    return $result > 0;
}

// Menu Painel ADM

add_action('admin_menu', 'comprarcurso_add_monitoramento_menu');

function comprarcurso_add_monitoramento_menu() {
    add_menu_page(
        'Monitoramento de Cursos',
        'Monitoramento de Cursos',
        'manage_options',
        'comprarcurso-monitoramento',
        'comprarcurso_render_monitoramento_page',
        'dashicons-visibility',
        51
    );
}

function comprarcurso_render_monitoramento_page() {
    global $wpdb;
    $table_pedidos = $wpdb->prefix . 'comprarcurso_pedidos';
    $table_acessos = $wpdb->prefix . 'comprarcurso_acessos';
    $users_table = $wpdb->prefix . 'users';

    $total_pagos = $wpdb->get_var("SELECT COUNT(*) FROM $table_pedidos WHERE status = 'pago'");
    $total_vendas = $wpdb->get_var("SELECT SUM(valor) FROM $table_pedidos WHERE status = 'pago'");

    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    $sql = "
        SELECT 
            u.display_name AS aluno,
            p.id AS pedido_id,
            c.post_title AS nome_curso,
            p.valor,
            p.status AS status_pedido,
            p.data AS data_pedido,
            a.status AS status_acesso,
            a.data_liberacao
        FROM $table_pedidos p
        INNER JOIN $users_table u ON p.user_id = u.ID
        INNER JOIN {$wpdb->prefix}posts c ON p.curso_id = c.ID
        LEFT JOIN $table_acessos a ON p.id = a.pedido_id
        WHERE 1 = 1
    ";

    if ($search) {
        $sql .= $wpdb->prepare(" AND u.display_name LIKE %s", '%' . $wpdb->esc_like($search) . '%');
    }

    $sql .= " ORDER BY p.data DESC";

    $resultados = $wpdb->get_results($sql);
    ?>

    <div class="wrap">
        <h1>📊 Monitoramento de Cursos</h1>

        <div style="display: flex; gap: 20px; margin: 20px 0;">
            <div style="background: #fff; border: 1px solid #ddd; border-left: 5px solid #46b450; padding: 15px 20px; border-radius: 6px; flex: 1;">
                <strong style="font-size: 18px;">💰 Cursos pagos</strong>
                <div style="font-size: 24px; margin-top: 5px;"><?php echo intval($total_pagos); ?></div>
            </div>
            <div style="background: #fff; border: 1px solid #ddd; border-left: 5px solid #0073aa; padding: 15px 20px; border-radius: 6px; flex: 1;">
                <strong style="font-size: 18px;">🎓 Total vendido</strong>
                <div style="font-size: 24px; margin-top: 5px;">R$ <?php echo number_format($total_vendas ?: 0, 2, ',', '.'); ?></div>
            </div>
        </div>

        <p>Veja abaixo os cursos vendidos, acessos liberados e status de cada aluno.</p>

        <form method="get" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="comprarcurso-monitoramento" />
            <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Buscar por nome do aluno" />
            <input type="submit" class="button" value="Buscar" />
        </form>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Aluno</th>
                    <th>Curso</th>
                    <th>Pedido</th>
                    <th>Valor</th>
                    <th>Status Pedido</th>
                    <th>Status Acesso</th>
                    <th>Data Pedido</th>
                    <th>Data Liberação</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultados): ?>
                    <?php foreach ($resultados as $r): ?>
                        <tr>
                            <td><?php echo esc_html($r->aluno); ?></td>
                            <td><?php echo esc_html($r->nome_curso); ?></td>
                            <td>#<?php echo esc_html($r->pedido_id); ?></td>
                            <td><?php echo esc_html(number_format($r->valor, 2, ',', '.')); ?></td>
                            <td>
                                <?php 
                                $cor = $r->status_pedido === 'pago' ? 'green' : ($r->status_pedido === 'pendente' ? 'orange' : 'red');
                                echo '<strong style="color:' . esc_attr($cor) . ';">' . esc_html($r->status_pedido) . '</strong>';
                                ?>
                            </td>
                            <td><?php echo esc_html($r->status_acesso ?: '—'); ?></td>
                            <td><?php echo esc_html($r->data_pedido); ?></td>
                            <td><?php echo esc_html($r->data_liberacao ?: '—'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">Nenhum registro encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
}

