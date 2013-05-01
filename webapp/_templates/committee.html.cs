<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Visiting Committees &amp; Councils | The University of Chicago</title>
		<?cs include:"inc/head.html.cs" ?>	
	</head>
	<body class="two-col-temp">
		<div id="page-wrapper">
			<div id="page">
				
				<?cs include:"inc/header.html.cs" ?>
				
				<div id="main-wrapper" class="">
					<div id="main" class=" container_12">
						<div id="content" class="column grid_9 push_3 ">
						<?cs if:authentication_error ?>
							<div class="error"><?cs var:authentication_error ?></div>
						<?cs /if ?>	
							<h1 id="page-title"><?cs var:Committee ?></h1>
							<div id="bottomrow" class="">
								<div class="grid_9 alpha">
									<div class="content region-content">
									<!--<h2 id="cmtname"><?cs var:Committee ?></h2>-->
									<p><em>*Denotes a Life Member</em></p>
									<p><strong><?cs var:Chairman ?></strong></p>
									<ul>
									  <?cs each:m=CommitteeMember ?>
									  <li><?cs var:m.FirstName ?> <?cs var:m.MiddleName ?> <?cs var:m.LastName ?></li>
									 
								  	 <?cs /each ?>
								  	</ul>
										<div class="clear" id=""></div>
									</div>
								</div>
							</div>
							<!-- bottomrow -->
						</div>
						<!-- /#content -->
						
						<div id="sidebar-first" class="column sidebar grid_3 pull_9">
							<?cs include:"inc/login.html.cs" ?>
							<?cs include:"inc/navigation-public.html.cs" ?>
							<!--<?cs include:"inc/search.html.cs" ?>-->
							<?cs include:"inc/related-links.html.cs" ?>
							
						</div><!-- /#sidebar-first -->
					</div><!-- /#main -->
				</div><!-- /#main-wrapper -->
				<div id="clearfoot"><!-- This is for the sticky footer --></div>
			</div><!-- /#page -->
		</div><!-- /#page-wrapper -->

	<?cs include:"inc/footer.html.cs" ?>
	<!-- Insert all js calls here  (jquery is included in the footer inc) -->
	
	</body>
</html>
	