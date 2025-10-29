<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/../vendor/autoload.php';

global $wpdb;

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$slug = isset($_GET['curso']) ? sanitize_title($_GET['curso']) : '';
if (!$slug) wp_die('Curso não especificado.');

$curso = get_page_by_path($slug, OBJECT, 'curso');
if (!$curso) wp_die('Curso não encontrado.');

$user = wp_get_current_user();
if (!$user->ID) {
    wp_redirect(wp_login_url($_SERVER['REQUEST_URI']));
    exit;
}

$preco = (float) get_post_meta($curso->ID, '_preco_curso', true);
if ($preco <= 0) wp_die('Preço inválido.');

$thumbnail = get_the_post_thumbnail_url($curso->ID, 'full');
if (!$thumbnail) {
    $thumbnail = get_template_directory_uri().'/assets/images/sem-thumb.png';
}

$table_pedidos = $wpdb->prefix . 'comprarcurso_pedidos';

// Cria pedido pendente
$wpdb->insert($table_pedidos, [
    'user_id' => $user->ID,
    'curso_id' => $curso->ID,
    'valor' => $preco,
    'status' => 'pendente',
]);
$pedido_id = $wpdb->insert_id;

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'mode' => 'payment',
        'customer_email' => $user->user_email,
        'line_items' => [[
            'price_data' => [
                'currency' => 'brl',
                'unit_amount' => intval($preco * 100),
                'product_data' => [
                    'name' => $curso->post_title,
                    'description' => 'Acesso completo ao curso "' . $curso->post_title . '"',
                    // 'images' => [$thumbnail]
                ],
            ],
            'quantity' => 1,
        ]],
        'metadata' => [
            'pedido_id' => $pedido_id,
            'user_id' => $user->ID,
            'curso_id' => $curso->ID,
        ],
        //Para PRODUÇÃO deve-se utilizar webhook
        'success_url' => home_url('/compra-concluida?session_id={CHECKOUT_SESSION_ID}'),
        'cancel_url' => home_url('/compra-cancelada'),
    ]);

    wp_redirect($session->url);
    exit;
} catch (Exception $e) {
    wp_die('Erro ao criar checkout: ' . esc_html($e->getMessage()));
}
