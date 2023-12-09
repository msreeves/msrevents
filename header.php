<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
		   <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
           <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
           <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		  <script type="text/javascript">
    		var wp_ajax = "<?php echo admin_url('admin-ajax.php'); ?>";
			</script>   
    <title>MSR Events</title>
        <?php wp_head ();?>
</head>
<body>
 <?php get_template_part( 'templates/partials/leaderboard/header' ); ?>	
	<header id="masthead" class="site-header">
    <nav class="navbar navbar-expand-lg navbar-light p-4">
        <div class="container-fluid">
              <a class="navbar-brand" href="<?php echo home_url(); ?>">
                <?php
			echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full' ) ?></a>
             <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="icon-bar top-bar"></span>
          <span class="icon-bar middle-bar"></span>
          <span class="icon-bar bottom-bar"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="navbar-nav ms-auto">
                    <?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
                    'container_id' => 'cssmenu', 
	                'walker' => new CSS_Menu_Walker()
				)
			); ?>
                </div>
            </div>
        </div>
    </nav>
	</header>

    
