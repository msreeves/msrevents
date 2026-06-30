<?php
/**
 * ACF: Flexible Content > Layouts > Key Point list 
 * 
 * @package WordPress
 * @subpackage QORP
 */ 

$columns = $args['keypoint'];
?>

<section>
  <div class="container">
    <div class="row">
    <?php foreach( $columns as $column ):   
       $heading = $column['title'];
       $icon = $column['icon'];
       $number = $column['number'];
       $introduction = $column['introduction']; ?>
       <div class="col-xl-3 mx-auto">
        <div class="post panel">
          <div class="icon">
          <img src="<?php echo $icon; ?>" alt="" />
           </div>
        <div class="listing-text text-center">
           <?php if ( $number ) : ?>
        <h2 class="count"> <?php echo $number; ?></h2>
        <?php endif; ?>
        <h2><?php echo $heading; ?></h2>		
          <p><?php echo $introduction; ?></p>
           </div>
     </div>
    </div>
    <?php endforeach; ?>
     </div>
  </div>
</section>