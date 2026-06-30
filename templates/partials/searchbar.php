  <div class="p-5">
 <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
       <div class="input-group ">
            <label class="screen-reader-text" for="s"><?php _x( 'Search for:', 'label' ); ?></label>
            <input class="form-control" size="10"  type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Search....." />
            <input type="submit" id="searchsubmit" value="Go" />
        </div>
    </form>
        </div>