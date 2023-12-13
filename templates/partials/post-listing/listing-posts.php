    <div class="col-lg-4">
    <div class="post panel">  
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
            <h3><?php the_title() ?></h3>                  
                      <p><?php echo wpse_custom_excerpts(30); ?></p>
                      <a href="<?php echo the_permalink(); ?>"><button>Read more</button></a>
                    </div>
                </div>
                    </div>