<?php function search_excerpt_highlight() {
    $excerpt = get_the_excerpt();
    $keys = implode('|', explode(' ', get_search_query()));
    $excerpt = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $excerpt);

    echo '<p>' . $excerpt . '</p>';
}

function search_title_highlight() {
    $title = get_the_title();
    $keys = implode('|', explode(' ', get_search_query()));
    $title = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $title);

    echo $title;
}

function search_content_highlight() {
        $content =  get_the_content();
        $keys = implode('|', explode(' ', get_search_query()));
        $content = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $content);

        echo '<p>' . $content . '.....' . '</p>';
    }

    function remove_pages_from_search() {
    global $wp_post_types;
    $wp_post_types['page']->exclude_from_search = true;
}
add_action('init', 'remove_pages_from_search');
    ?>