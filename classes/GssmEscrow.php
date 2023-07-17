<?php

class GssmEscrow extends GssmBase {

    protected $postType = 'gssm_escrow';

    function create_post_type() {
        $args = [
            'label'  => esc_html__( 'GSSM Escrows', 'gssm' ),
            'public'  => true,
            'rewrite' => true,
            'capabilities' => [
                'publish_posts' => 'publish_posts',
                'edit_posts' => 'edit_posts',
                'edit_others_posts' => 'edit_others_posts',
                'read_private_posts' => 'read_private_posts',
                'edit_post' => 'edit_post',
                'read_post' => 'read_post',
                'edit_published_posts' => 'edit_published_posts',
                'create_posts' => 'do_not_allow',
            ],
            'map_meta_cap' => true,
            'supports' => [
                'title'
            ],
        ];
    
        register_post_type($this->postType, $args);
    }

}

$gssmEscrow = new GssmEscrow();

?>