<?php

/************* CUSTOM POST TYPES FOR WILSON FIELD SITE *****************/
register_post_type( 'editions', array( 'label' => 'Software Editions', 'public' => true, 'supports' => array('title','editor','thumbnail','page-attributes') ) );

function vipx_remove_cpt_slug( $post_link, $post, $leavename ) {

    if ( ! in_array( $post->post_type, array( 'lpage','offices','' ) ) || 'publish' != $post->post_status )
        return $post_link;

    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

    return $post_link;
}
add_filter( 'post_type_link', 'vipx_remove_cpt_slug', 10, 3 );


function vipx_parse_request_tricksy( $query ) {

    // Only noop the main query
    if ( ! $query->is_main_query() )
        return;

    // Only noop our very specific rewrite rule match
    if ( 2 != count( $query->query )
        || ! isset( $query->query['page'] ) )
        return;

    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
    if ( ! empty( $query->query['name'] ) )
        $query->set( 'post_type', array( 'post', 'lpage', 'offices', 'page', 'advice' ) );
}
add_action( 'pre_get_posts', 'vipx_parse_request_tricksy' );

?>
