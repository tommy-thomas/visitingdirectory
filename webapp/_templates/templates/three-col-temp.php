<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title>UChicago Template | The University of Chicago</title>
	
	<?php include("inc/head.php"); ?>
	</head>
	
	<body class="three-col-temp">
		<div id="page-wrapper">
			<div id="page">
				
			<?php include("inc/header.php"); ?>
				
				<div id="main-wrapper" class="">
					<div id="main" class=" container_12">
						<div id="content" class="column grid_9 push_3 ">
						
							<h1 id="page-title">
								<!-- EDIT THE PAGE TITLE HERE -->
								By the Numbers
							</h1>
							<div id="bottomrow" class="">
								<div class="grid_6 alpha">
									
									<div class="content region-content">
										<!-- CONTENT GOES HERE-->
									</div>
								
								</div>
								
								<div id="sidebar-second" class="column sidebar grid_3 omega">
									<!-- RIGHT SIDE CONTENT GOES HERE -->
								
								</div><!-- /#sidebar-second -->
							
							</div><!-- bottomrow -->
						</div><!-- /#content -->
						
						<div id="sidebar-first" class="column sidebar grid_3 pull_9">
							<!-- LEFT COLUMN CONTENT GOES HERE -->
							
							<?php include("inc/login.php"); ?>
							<?php include("inc/navigation.php"); ?>
							<?php include("inc/search.php"); ?>
							<?php include("inc/related-links.php"); ?>
							
								
						</div><!-- /#sidebar-first -->
					</div><!-- /#main -->
				</div><!-- /#main-wrapper -->
				
				<div id="clearfoot"><!-- This is for the sticky footer --></div>
			</div><!-- /#page -->
		</div><!-- /#page-wrapper -->
		
	<?php include("inc/footer.php"); ?>

	<!-- Insert all js calls here  (jquery is included in the footer inc) -->
	
	<script type="text/javascript" src="scripts/lightbox.js"></script>	
	
	<script type="text/javascript">
	    $(function() {
			$('a.lightbox').lightBox(); // Select all links that contains lightbox in the attribute rel
	    });
    </script>

	</body>
</html>
