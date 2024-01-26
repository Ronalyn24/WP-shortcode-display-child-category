<?php

function my_theme_enqueue_styles() {

    $parent_style = 'cwicly';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ), wp_get_theme()->get('1.0.0'));
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );



function custom_child_taxonomy_shortcode($atts) {
    
    // example:  echo do_shortcode [custom_display_child_taxonomy parent_category="tipo" child_categories="aventura,cultural-arte,escapadas,esqui"]
    
    ob_start();
    

    echo '<style>
            .cards { 
                display: flex; 
                flex-wrap: wrap;
                gap: 42px;
            }
            
            .cards__item {
                width: 22.666%;
                margin-bottom: 2rem;
            }
            
            .cards__image {
                position: relative;
                padding-top: 40vh;
                margin-bottom: 1.3rem;
            }
            
            .cards__image img {
                object-fit: cover;
                width: 100%;
                height: 100%;
                position: absolute;
                top: 0;
                left: 0;
            }
            
        </style>';
        
    echo '<div class="cards">';

        // Default attributes
    $atts = shortcode_atts(
        array(
            'parent_category' => 'tipo',
            'child_categories' => 'aventura,cultural-arte,escapadas,esqui',
        ),
        $atts,
        'custom_experience'
    );

    // Get the parent category
    $parent_category = get_term_by('slug', $atts['parent_category'], 'experience-type');

    if ($parent_category) {
        
        // Explode the comma-separated child categories into an array
        $specific_child_slugs = explode(',', $atts['child_categories']);

        // Loop through specific child slugs
        foreach ($specific_child_slugs as $specific_child_slug) { 
            
            // Get the child category by slug
            $child_category = get_term_by('slug', $specific_child_slug, 'experience-type');

            // Check if the child category exists and is a child of the specified parent category
            if ($child_category && term_is_ancestor_of($parent_category->term_id, $child_category->term_id, 'experience-type')) {
                
                echo '<div class="cards__item">';
                
                    // Get ACF image thumbnail field value for the child category
                    echo '<div class="cards__image">';
                    
                        $image = get_field('image_thumbnail', $child_category);

                        if ($image) {
                            echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '"/>';
                        }
                    
                    echo '</div>';

                    // Display the child category title
                    echo '<h4>' . esc_html($child_category->name) . '</h4>';

                    // Display the child category archive link
                    echo '<a href="' . esc_url(get_term_link($child_category)) . '"><u>View Archive Page</u></a>';
                    
                echo '</div>';
            }
        }
    }
    
    echo '</div>';
    
    wp_reset_postdata();
    
    $post_content =  ob_get_contents();

    ob_end_clean();

    return $post_content;
 
}
add_shortcode('custom_display_child_taxonomy', 'custom_child_taxonomy_shortcode');
