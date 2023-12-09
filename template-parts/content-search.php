<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package msrevents
 */

?>

  <div class="col-lg-4">
    <div class="panel">  
        <div class="listing-image">
            	<?php the_post_thumbnail(); ?>
            </div>
            <div class="listing-text">
              <p> <?php foreach((get_the_category()) as $category) {
                echo '<span>'; 
                echo $category->cat_name . ' ';
                echo '</span>';
                } ?>
                 </p>  
            <h3> <?php echo search_title_highlight(); ?></h3>
            <?php echo search_excerpt_highlight(); ?>
            <a href="<?php echo the_permalink(); ?>"><button>Read more</button></a>
                    </div>
                </div>
                    </div>