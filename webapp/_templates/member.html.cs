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
	
	{{ include('inc/head.html.twig') }}
</head>  
<body>
	{{ include('inc/header.html.twig') }}
	<div class="container">
		<h1 class="page-title">Secure Directory - beta</h1>
		<div class="row">
			<div class="span9 pull-right">
				<div class="maincontent">
					<p>Didn't find what you were looking for? <a href="search.php">Start a new search.</a><br/>
					Is your info incorrect or incomplete? <a href="mailto:spaepen@uchicago.edu?subject=Update for Visiting Committee Member">Contact us with an update.</a></p>

					<h2>Member Information</h2>
					<p>Please Note: Member data is not available for printing at this time. Please contact Alumni Relations &amp; Development if you need a copy of this information.</p>
					
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
							<p>
								<?cs each:c=committee_list ?>												  
								<a href="results.php?c=<?cs var:c.key ?>"><?cs var:c.value ?></a><br />
								<?cs /each ?>
							</p>
						<?cs /each ?>
					</div><!-- end member data div -->
				</div>
			</div>
			<div class="span3">				
				{{ include('inc/navigation.html.twig') }} <!-- this now includes the related links as well -->
				{{ include('inc/related-links.html.twig') }}
			</div>					
		</div>
	</div><!-- /#page-wrapper -->
	<div class="push"><!--//--></div>

	{{ include('inc/footer.html.twig') }}

<!-- Insert all js calls here  (jquery is included in the footer inc) -->

</body>
</html>
	