<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/../vendor/autoload.php';
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

global $wpdb;
$table_pedidos = $wpdb->prefix . 'comprarcurso_pedidos';
$table_acessos = $wpdb->prefix . 'comprarcurso_acessos';

$session_id = isset($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : '';

if (!$session_id) {
    wp_die('Sessão inválida.');
}

try {
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    $meta = $session->metadata;

    if ($session->payment_status === 'paid') {
        $pedido_id = intval($meta->pedido_id);
        $user_id = intval($meta->user_id);
        $curso_id = intval($meta->curso_id);

        $wpdb->update($table_pedidos, ['status' => 'pago'], ['id' => $pedido_id]);

        // Cria registro na tabela de acesso curso
        $wpdb->insert($table_acessos, [
            'user_id' => $user_id,
            'curso_id' => $curso_id,
            'pedido_id' => $pedido_id,
            'status' => 'ativo',
        ]);

        wp_redirect(add_query_arg('toast', 'curso-liberado', site_url('/meus-cursos')));
    } else {
        wp_redirect(add_query_arg('toast', 'pagamento-pendente', site_url('/meus-cursos')));
    }

} catch (Exception $e) {
    wp_die('Erro ao verificar pagamento: ' . esc_html($e->getMessage()));
}
