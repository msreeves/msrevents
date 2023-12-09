<div class="row g-0">
     <div class="col-md-6"> 
        <?php if ( get_field('location') ) : ?>
                                     <div class="ratio ratio-16x9">
                                 <?php echo get_field('location') ?>
                                    </div>
                                     <?php endif; ?> 
    </div>
     <div class="col-md-6">
        <div class="panel">
            <div class="my-auto text-center">
        			    <?php the_title( '<h1 class="entry-title">', '</h1>' );?>
							 <?php if ( get_field('event_date') ) : ?> 
								<span> <i class="fa fa-calendar p-2" aria-hidden="true"></i> <span> <?php print get_field('event_date') ?></span>
									 <?php endif; ?> 
								<?php if ( get_field('event_time') ) : ?>
								 <span><i class="fa fa-clock p-2" aria-hidden="true"></i><?php print get_field('event_time') ?></span> 
									 <?php endif; ?>
									<?php if ( get_field('venue') ) : ?>
            					 <span> <i class="fa fa-map-marker p-2" aria-hidden="true"></i><?php print get_field('venue') ?></span> 
									 <?php endif; ?> 
                        </div>
                </div>
        </div>
    </div>
    <?php if ( get_field('information') ) : ?>
        <div class="entry-content">
    <?php print get_field('information') ?>
    </div>
     <?php endif; ?>
<?php if ( get_field('image_gallery') ) : ?>
    <?php get_template_part( 'inc/components/gallery' ); ?>
     <?php endif; ?>