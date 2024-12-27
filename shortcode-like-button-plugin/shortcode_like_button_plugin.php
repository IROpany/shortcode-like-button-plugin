<?php
/*
Plugin Name: Shortcode Like Button Plugin
Description: Adds a Shortcode like button to posts.
Version: 2.0
Author: Chick
Author URI: https://iropany.com/Chick/
Domain Path: languages
Text Domain: wp-total-hacks
*/

// ファイルへの直接アクセスを防止
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

// スクリプトを読み込む
function shortcode_like_button_enqueue_scripts() {
    wp_enqueue_script('shortcode-like-button', plugin_dir_url(__FILE__) . 'js/shortcode-like-button.js', array('jquery'), false , true); // カスタムスクリプトを読み込む
    wp_localize_script('shortcode-like-button', 'shortcode_like_button_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('shortcode-like-button-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'shortcode_like_button_enqueue_scripts');

// ショートコードを処理する関数
function shortcode_like_button_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(), // 引数から投稿IDを受け取る
    ), $atts);

    $post_id = absint($atts['post_id']);
    if (!$post_id) {
        return ''; // 投稿IDが無効なら何も出力しない
    }

    $like_count = get_post_meta($post_id, 'shortcode_like_count', true);
    $like_count = $like_count ?: 0;

    $button_html = '
        <p>いいねの数: <span class="like-count-' . esc_attr($post_id) . '">' . esc_html($like_count) . '</span></p>
        <button class="my-like-button" data-post-id="' . esc_attr($post_id) . '" data-action="increment_likes">' . esc_html__('いいね', 'shortcode-like-button') . '</button>
    ';

    return $button_html;
}
add_shortcode('shortcode_like_button', 'shortcode_like_button_shortcode');

// いいねカウントを増やすためのAJAX処理
function increment_likes() {
    check_ajax_referer('shortcode-like-button-nonce', 'security');

    $post_id = absint($_POST['post_id']);
    if (!$post_id) {
        wp_die('Invalid post ID');
    }

    $like_count = get_post_meta($post_id, 'shortcode_like_count', true);
    $like_count = $like_count ? $like_count + 1 : 1;

    update_post_meta($post_id, 'shortcode_like_count', $like_count);

    echo $like_count;
    wp_die();
}
add_action('wp_ajax_increment_likes', 'increment_likes');
add_action('wp_ajax_nopriv_increment_likes', 'increment_likes');
?>
