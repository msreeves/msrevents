<?php
/**
 * Displays the next and previous post navigation in single posts.
 *
 * @package WordPress
 * @subpackage msrevents
 * @since msrevents 1.0
 */

$next_post = get_next_post();
$prev_post = get_previous_post();

if ( $next_post || $prev_post ) {

	$pagination_classes = '';

	if ( ! $next_post ) {
		$pagination_classes = ' only-one only-prev';
	} elseif ( ! $prev_post ) {
		$pagination_classes = ' only-one only-next';
	}

	?>

	<nav class="pagination-single section-inner<?php echo esc_attr( $pagination_classes ); ?>" aria-label="<?php esc_attr_e( 'Post', 'msrsandbox' ); ?>">

		<hr class="styled-separator is-style-wide" aria-hidden="true" />
        <h1> Related <?php $category = get_the_category(); echo $category[0]->cat_name; ?> </h1>
		<div class="pagination-single-inner">
<div class="row">            

		<?php $post_id = $post->ID; // current post id
$cat = get_the_category(); 
$current_cat_id = $cat[0]->cat_ID; // current category Id 

$args = array('category'=>$current_cat_id,'orderby'=>'post_date','order'=> 'DESC');
$posts = get_posts($args);
// get ids of posts retrieved from get_posts
$ids = array();
foreach ($posts as $thepost) {
    $ids[] = $thepost->ID;
}
// get and echo previous and next post in the same category
$thisindex = array_search($post->ID, $ids);
$previd = $ids[$thisindex-1];
$nextid = $ids[$thisindex+1];


if (!empty($previd)){
?>

  <div class="col-lg-6 mx-auto">
        <div class="post panel">  
        <div class="listing-image">
            	<?php echo wp_kses_post( get_the_post_thumbnail( $previd ) ); ?>
                      			         <?php
if(in_category(10)){
?>
<span class="sponsored">This is Sponsored content</span>
<?php } ?> 
            </div>
            <div class="listing-text">
                     <p> <?php $cat_name = 'category';
       $categories = get_the_terms( $previd, $cat_name );
       foreach($categories as $category) {
         if($category->parent){
            echo '<a href="' . esc_url( get_category_link( $category ) ) . '"><span>' . $category->name . '</span></a>';
         }
       }  
?> 
                 </p>  
            <h3><?php echo wp_kses_post( get_the_title( $previd ) ); ?></h3>                   
                     <p><?php echo wp_kses_post ( get_the_excerpt( $previd ) ); ?></p>
                      <a href="<?php echo esc_url( get_permalink( $previd ) ); ?>"><button>Read more</button></a>
                    </div>
                </div>
                      </div>
<?php
}
if (!empty($nextid)){
?>

  <div class="col-lg-6 mx-auto">
        <div class="post panel">  
        <div class="listing-image">
            	<?php echo wp_kses_post( get_the_post_thumbnail( $nextid ) ); ?>
                  			         <?php
if(in_category(10)){
?>
<span class="sponsored">This is Sponsored content</span>
<?php } ?> 
            </div>
            <div class="listing-text">
                    <p> <?php $cat_name = 'category';
       $categories = get_the_terms( $nextid, $cat_name );
       foreach($categories as $category) {
         if($category->parent){
            echo '<a href="' . esc_url( get_category_link( $category ) ) . '"><span>' . $category->name . '</span></a>';
         }
       }  
?> 
                 </p>  
            <h3><?php echo wp_kses_post( get_the_title( $nextid ) ); ?></h3>                
                     <p><?php echo wp_kses_post ( get_the_excerpt( $nextid ) ); ?></p>
                      <a href="<?php echo esc_url( get_permalink( $nextid ) ); ?>"><button>Read more</button></a>
                    </div>
                </div>
                      </div>
<?php
}
?>

		</div><!-- .pagination-single-inner -->

		<hr class="styled-separator is-style-wide" aria-hidden="true" />
</div>
	</nav><!-- .pagination-single -->

	<?php
}