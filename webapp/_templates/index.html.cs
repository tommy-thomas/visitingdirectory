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
				<div class="content region-content">									
					<p>Please log in below to use the directory. If you do not have a CNetID, you may log in with an account you already have with Google, Yahoo, or Facebook. <em>Please note that the social media account you choose should use an email address we have on file.</em> If you have any trouble logging in, please contact us at <a href="mailto:weberror@uchicago.edu?subject=Visiting Committees Directory login problem">weberror@uchicago.edu</a>.</p>
					
					<?cs if:authentication_error ?>
						<div class="alert alert-block"><?cs var:authentication_error ?></div>
					<?cs /if ?>	
					
					<p><a href="<?cs var:domain ?>/Shibboleth.sso/Login?entityID=urn:mace:incommon:uchicago.edu&target=<?cs var:base ?>index.php"><strong>Login with CNetID / UCHADID</strong></a></p>
					
					<p>Or log in using an existing account below*:</p>
					<ul id="login-list">
						<li class="facebook">
							<a href="https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/SSOService.php?spentityid=<?cs var:domain ?>/shibboleth&source=facebook&RelayState=<?cs var:base ?>index.php">Facebook</a>
						</li>
						<li class="google">
							<a href="https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/SSOService.php?spentityid=<?cs var:domain ?>/shibboleth&source=google&RelayState=<?cs var:base ?>index.php">Google</a></li>
			
						
						<li class="yahoo">
							<a href="https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/SSOService.php?spentityid=<?cs var:domain ?>/shibboleth&source=yahoo&RelayState=<?cs var:base ?>index.php">Login with Yahoo</a>
						</li>	
					</ul>

					<p>* <em>Please note that the account you choose should use an email address we have on file.</em> </p>
					
					<h2>Need Help?</h2>
					<ul>
						<li>I have an account above, but it's not linked to an email address on file.</li>
						<ul>
							<li><a href="mailto:spaepen@uchicago.edu?subject=VC Directory - add email address to Griffin">Contact us</a> and give us the email address we're missing and we will add it to your record.</li>
						</ul>
						<li>I'd like to create an account above, but I don't know what email address you have on file for me.</li>
						
						<ul>
							<li><a href="mailto:mailto:spaepen@uchicago.edu?subject=VC Directory - request for email addresses in Griffin">Contact us</a> and we'll let you know what email address we have on file for you.</li>
						</ul>	
					</ul>								
				</div>
			</div>	
			<div class="span3">				
				<?cs include:"inc/navigation.html.cs" ?> <!-- this now includes the related links as well -->
			</div>					
		</div>
	</div><!-- /#page-wrapper -->
	<div class="push"><!--//--></div>

	<?cs include:"inc/footer.html.cs" ?>

<!-- Insert all js calls here  (jquery is included in the footer inc) -->

</body>
</html>
	