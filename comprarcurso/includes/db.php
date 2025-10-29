<?php
function comprarcurso_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_pedidos = $wpdb->prefix . 'comprarcurso_pedidos';
    $table_acessos = $wpdb->prefix . 'comprarcurso_acessos';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // chamada DBDElta
    $sql1 = "CREATE TABLE $table_pedidos (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        curso_id BIGINT UNSIGNED NOT NULL,
        valor DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pendente',
        data DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql1);

    $sql2 = "CREATE TABLE $table_acessos (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        curso_id BIGINT UNSIGNED NOT NULL,
        pedido_id BIGINT UNSIGNED DEFAULT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'ativo',
        data_liberacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql2);
}
