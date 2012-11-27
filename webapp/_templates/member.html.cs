 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Visiting Committees Directory | Board of Trustees | The University of Chicago</title>
		<?cs include:"inc/head.html.cs" ?>	
	</head>
	<body class="two-col-temp">
		<div id="page-wrapper">
			<div id="page">
				
				<?cs include:"inc/header.html.cs" ?>
				
				<div id="main-wrapper" class="">
					<div id="main" class=" container_12">
						<div id="content" class="column grid_9 push_3 ">
						
							<h1 id="page-title">Visiting Committees Directory - beta</h1>
							<div id="bottomrow" class="">
								<div class="grid_9 alpha">
									<div class="content region-content">
									<p>Didn't find what you were looking for? <a href="search.php">Start a new search.</a></p>
									<p>Is your info incorrect or incomplete? <a href="mailto:spaepen@uchicago.edu?subject=Update for Visiting Committee Member">Contact us with an update.</a></p>
									
										<h2 id="title">Member Information</h2>
										<p class="hiddenscreen">Please Note: Member data is not available for printing at this time. Please contact Alumni Relations &amp; Development if you need a copy of this information.</p>
										<div id="memberdata">
										
										<?cs each:m=CommitteeMember ?>
										
											<p>
												<strong><?cs var:m.FullName ?></strong><br />
												<?cs if:m.DegreeInfo ?><?cs var:m.DegreeInfo ?><br /><?cs /if ?>
												<?cs if:m.JobTitle ?><?cs var:m.JobTitle ?><br /><?cs /if ?>
												<?cs if:m.EmployerName ?><?cs var:m.EmployerName ?><br /><?cs /if ?>
											</p>
											<h3>Preferred Contact Information</h3>
											<p>
												<?cs if:m.Email ?><a href="mailto:<?cs var:m.Email ?>"><?cs var:m.Email ?></a><br /><?cs /if ?>
												<?cs if:m.PhoneNumber ?>Phone: <a href="tel:<?cs var:m.PhoneNumber ?>"><?cs var:m.PhoneNumber ?></a><?cs /if ?>
											</p>
											<h3>Preferred Mailing Address</h3>
											<p>
												<?cs if:m.StreetOne ?><?cs var:m.StreetOne ?><?cs /if ?>
												<?cs if:m.StreetTwo ?><br /><?cs var:m.StreetTwo ?><?cs /if ?>
												<?cs if:m.StreetThree ?><br /><?cs var:m.StreetThree ?><?cs /if ?>
												<br />
												<?cs if:m.City ?><?cs var:m.City ?>,<?cs /if ?> <?cs if:m.State ?><?cs var:m.State ?><?cs /if ?> <?cs if:m.Zip ?><?cs var:m.Zip ?><?cs /if ?><?cs if:m.ForeignCityZip ?><?cs /if ?><?cs var:m.ForeignCityZip ?><?cs if:m.CountryCode ?>, <?cs var:m.CountryCode ?><?cs /if ?>
											</p>
										
											<h3>Committees:</h3>
											<p><?cs each:c=committee_list ?>	
												    <?cs var:c ?><br />
												<?cs /each ?>
											</p>
										<?cs /each ?>
 										</div><!-- end member data div -->

									
									</div>
								</div>
							</div>
							<!-- bottomrow -->
						</div>
						<!-- /#content -->
						
						<div id="sidebar-first" class="column sidebar grid_3 pull_9">
							<?cs include:"inc/login.html.cs" ?>
							<?cs include:"inc/navigation.html.cs" ?>
							<!--<?cs include:"inc/search.html.cs" ?> -->
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
	