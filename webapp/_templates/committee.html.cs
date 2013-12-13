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
		<?cs if:authentication_error ?>
			<div class="error"><?cs var:authentication_error ?></div>
		<?cs /if ?>	
		<h1 class="page-title"><?cs var:Committee ?></h1>
		<div class="row">
			<div class="span9 pull-right">
				<div class="maincontent">
					<!--<h2 id="cmtname"><?cs var:Committee ?></h2>-->
					
					<h2><?cs var:Chairman ?></h2>
					<p class="small"><em>*Denotes a Life Member</em></p>
					<ul class="list-3col">
						<?cs each:m=CommitteeMember ?>
							<li>
								<?cs var:m.FirstName ?> <?cs var:m.MiddleName ?> <?cs var:m.LastName ?>
							</li>		 
						<?cs /each ?>
					</ul>
				</div>
			</div>
			
			<div class="span3">				
				<?cs include:"inc/navigation-public.html.cs" ?> <!-- this now includes the related links as well -->
				<?cs include:"inc/related-links.html.cs" ?>
			</div>					
					
		</div>
	</div><!-- /#page-wrapper -->
	<div class="push"><!--//--></div>

	<?cs include:"inc/footer.html.cs" ?>

<!-- Insert all js calls here  (jquery is included in the footer inc) -->

</body>
</html>
	