<?php
// プラグインがアンインストールされる場合のみ実行されるようにする
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// いいねカウントメタデータを削除する関数
function shortcode_like_button_delete_plugin() {
    global $wpdb;

    // すべての投稿メタデータを削除
    $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'shortcode_like_count'" );

    // 必要に応じて、プラグインで作成されたその他のオプションやデータを削除
    // delete_option('shortcode_like_button_option');
}

// アンインストール時にクリーンアップ関数を呼び出す
shortcode_like_button_delete_plugin();
?>
