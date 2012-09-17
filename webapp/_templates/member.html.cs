 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>University of Chicago Data | The University of Chicago</title>
		<?cs include:"inc/head.html.cs" ?>	
	</head>
	<body class="two-col-temp">
		<div id="page-wrapper">
			<div id="page">
				
				<?cs include:"inc/header.html.cs" ?>
				
				<div id="main-wrapper" class="">
					<div id="main" class=" container_12">
						<div id="content" class="column grid_9 push_3 ">
						
							<h1 id="page-title">Visiting Committees Directory</h1>
							<div id="bottomrow" class="">
								<div class="grid_9 alpha">
									<div class="content region-content">
										<h2>Member Information</h2>
										<p>Didn't find what you were looking for? <a href="search.php">Start a new search.</a></p>
										
										<?cs each:m=CommitteeMember ?>
											<p>
												<strong><?cs var:m.FirstName ?> <?cs var:m.MiddleName ?><?cs var:m.LastName ?></strong><br />
												<?cs var:m.DegreeInfo ?><br />
												<?cs var:m.JobTitle ?><br />
												<?cs var:m.EmployerName ?><br />
												<?cs var:m.Email ?><br />
												Phone: <?cs var:m.PhoneAreaCode ?> <?cs var:m.PhoneNumber ?>
											</p>
											<p>
												<?cs var:m.StreetOne ?><br />
												<?cs var:m.City ?>, <?cs var:m.State ?> <?cs var:m.Zip ?><br />
												<strong>Committees:</strong><br />
												<?cs each:c=committee_list ?>	
												<?cs var:c ?><br />
												<?cs /each ?>
											</p>
										<?cs /each ?>
 

									
									</div>
								</div>
							</div>
							<!-- bottomrow -->
						</div>
						<!-- /#content -->
						
						<div id="sidebar-first" class="column sidebar grid_3 pull_9">
							<?cs include:"inc/login.html.cs" ?>
							<?cs include:"inc/navigation.html.cs" ?>
							<?cs include:"inc/search.html.cs" ?>
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
	