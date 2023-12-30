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
        <div class="listing-image">
          <div class="icon">
          <img src="<?php echo $icon; ?>" alt="" />
           </div>
        </div>
        <div class="listing-text text-center my-auto">
           <?php if ( $number ) : ?>
        <h1 class="count"> <?php echo $number; ?></h1>
        <?php endif; ?>
        <h3><?php echo $heading; ?></h3>		
          <p><?php echo $introduction; ?></p>
           </div>
     </div>
    </div>
    <?php endforeach; ?>
     </div>
  </div>
</section>