 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Secure Visiting Committees Directory | The University of Chicago</title>
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
							<h1 id="page-title">Secure Visiting Committees Directory - beta</h1>
							<div id="bottomrow" class="">
								<div class="grid_9 alpha">
									<div class="content region-content">
									
									<div class="error">Please Note: The Visiting Committees Directory will be temporarily unavailable during a system upgrade  between Friday, April 25 and Sunday, April 29th. We apologize for the inconvenience.</div>
									
										<p>Please log in below to use the directory. If you do not have a CNetID, you may log in with an account you already have with Google, Yahoo, or Facebook. <em>Please note that the social media account you choose should use an email address we have on file.</em> If you have any trouble logging in, please contact us at <a href="mailto:weberror@uchicago.edu?subject=Visiting Committees Directory login problem">weberror@uchicago.edu</a>.</p>
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
										<li><em>I have an account above, but it's not linked to an email address on file.</em></li>
										<ul>
											<li><a href="mailto:">Contact us</a> and give us the email address we're missing and we will add it to your record.</li>
										</ul>
										<li><em>I'd like to create an account above, but I don't know what email address you have on file for me.</em></li>
										
											<ul><li><a href="mailto:">Contact us</a> and we'll let you know what email address we have on file for you.</li>
										</ul>
									</ul>
									
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
	