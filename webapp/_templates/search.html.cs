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
						<?cs if:authentication_error ?>
							<div class="error"><?cs var:authentication_error ?></div>
						<?cs /if ?>	
							<h1 id="page-title">Visiting Committees Directory</h1>
							<div id="bottomrow" class="">
								<div class="grid_9 alpha">
									<div class="content region-content">
										<p>You may browse the directory by committee or search for a committee member by name.</p>
										<h2>Browse by Committee</h2>
										<form action="results.php" method="post" name="by_committee">
											<div>
												<select name="committee" size="1">
													<option value="" selected>-- Select -- </option>
													<?cs each:c=Committee  ?>		     
													<option value="<?cs var:c.COMMITTEE_CODE ?>"><?cs var:c.SHORT_DESC ?></option>     	      			
													<?cs /each ?>
												</select>
												<input class="btn" type="submit" name="search_by_committee" value="Go" />
											</div>	
										</form>

										<h2>Search by Name</h2>
										<form action="results.php" method="post" name="by_name">
											<div class="w50 first" id="">
												<label for="f_name">First Name</label>
												<input name="f_name" type="text" />
											</div>
											<div class="w50" id="">
												<label for="l_name">Last Name</label>
												<input name="l_name" type="text" />
											</div>
										
											<div><input class="btn" type="submit" name="search_by_name" value="Search" /></div>
										</form>
									
									</div>
								</div>
							</div>
							<!-- bottomrow -->
						</div>
						<!-- /#content -->
						
						<div id="sidebar-first" class="column sidebar grid_3 pull_9">
							<?cs include:"inc/login.html.cs" ?>
							<?cs include:"inc/navigation.html.cs" ?>
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
	