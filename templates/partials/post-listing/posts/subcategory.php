		<div class="col-md-4">
			<div class="post panel">  
        <div class="listing-image">
              <?php get_template_part( 'templates/partials/featured-image' ); ?>
                    			         <?php
if(in_category(6)){
?>
<span class="sponsored">This is Sponsored content</span>
<?php } ?> 
            </div>
            <div class="listing-text">
              <p> <?php $cat_name = 'category';
       $categories = get_the_terms( $post->ID, $cat_name );
       foreach($categories as $category) {
         if($category->parent){
            echo '<a href="' . esc_url( get_category_link( $category ) ) . '"><span>' . $category->name . '</span></a>';
         }
       }  
?> 
                 </p>  
            <h3><?php the_title() ?></h3>                  
                     <p><?php echo wpse_custom_excerpts(30); ?></p>
                      <a href="<?php echo the_permalink(); ?>"><button>Read more</button></a>
                    </div>
                </div>
                    </div>