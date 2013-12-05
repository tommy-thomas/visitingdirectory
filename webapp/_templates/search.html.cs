<!doctype html>
<!--[if lt IE 7]><html class="no-js ie lt-ie10 lt-ie9 lt-ie8 lt-ie7" lang="en"><![endif]-->
<!--[if IE 7]><html class="no-js ie ie7 lt-ie9 lt-ie8" lang="en"><![endif]-->
<!--[if IE 8]><html class="no-js ie ie8 lt-ie9 lt-ie10" lang="en"><![endif]-->
<!--[if IE 9]><html class="no-js ie ie9  lt-ie10" lang="en"><![endif]-->
<!--[if IE 10]><html class="no-js ie ie10 lt-ie11" lang="en"><![endif]-->
<!--[if gt IE 10]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title>Secure Directory | Visiting Committees &amp; Councils | The University of Chicago</title>
	
	<?cs include:"inc/head.html.cs" ?>
</head>  
<body>
	<?cs include:"inc/header.html.cs" ?>
	<div class="container">
		<h1 class="page-title">Secure Directory - beta</h1>
			<div class="row">
				<div class="span9 pull-right">
					<div class="maincontent">
						<p>You may browse the directory by committee or search for a committee member by name.</p>
						<div class="row-fluid">
							<div class="span5">
								<h2>Browse by Committee</h2>
								<form action="results.php" method="post" name="by_committee">
									<div>
										<select name="committee" size="1">
											<option value="" selected>-- Select -- </option>
											<?cs each:c=Committee  ?>		     
											<option value="<?cs var:c.COMMITTEE_CODE ?>"><?cs var:c.SHORT_DESC ?></option>     	      			
											<?cs /each ?>
										</select>
										<div>
											<input class="btn" type="submit" name="search_by_committee" value="Go" />
										</div>
									</div>
								</form>
							</div>
							<div class="span5">
								<h2>Search by Name</h2>
								<form action="results.php" method="post" name="by_name">
									<div class="w50 first" id="">
										<label for="f_name">First Name</label>
										<input name="f_name" type="text" />
									</div>
									<div class="w50 first" id="">
										<label for="l_name">Last Name</label>
										<input name="l_name" type="text" />
									</div>
									<div>
										<input class="btn" type="submit" name="search_by_name" value="Search" />
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="span3">				
					<?cs include:"inc/navigation.html.cs" ?> <!-- this now includes the related links as well -->
					<?cs include:"inc/related-links.html.cs" ?>
				</div>					

			</div>	
		</div>
	</div><!-- /#page-wrapper -->
	<div class="push"><!--//--></div>

	<?cs include:"inc/footer.html.cs" ?>

<!-- Insert all js calls here  (jquery is included in the footer inc) -->

</body>
</html>
	