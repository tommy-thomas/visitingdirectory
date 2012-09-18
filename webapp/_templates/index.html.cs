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
										<p>Please log in below to use the directory. If you do not have a CNetID, you may log in with an account you already have with LinkedIn, Google, or Facebook. If you have any trouble logging in, please contact us at xxxxxxxx@uchicago.edu.</p>
										<p><a href="<?cs var:domain ?>/Shibboleth.sso/Login?entityID=urn:mace:incommon:uchicago.edu&target=<?cs var:base ?>index.php">Login with CNetID / UCHADID</a></p>
										<p>Or log in using an existing account below:</p>
										<ul>
											<li>
												<a href="https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/SSOService.php?spentityid=<?cs var:domain ?>/shibboleth&source=facebook&RelayState=<?cs var:base ?>index.php">Facebook</a>
											</li>
											<li>
												<a href="https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/SSOService.php?spentityid=<?cs var:domain ?>/shibboleth&source=google&RelayState=<?cs var:base ?>index.php">Google</a></li>
											<li>
												<a href="https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/SSOService.php?spentityid=<?cs var:domain ?>/shibboleth&source=linkedin&RelayState=<?cs var:base ?>index.php">Login with Linked-In</a></li>
											<!--// <li> <a href="https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/SSOService.php?spentityid=<?cs var:domain ?>/shibboleth&source=twitter&RelayState=<?cs var:base ?>index.php">Login with Twitter</a></li> //-->
											<li>
												<a href="https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/SSOService.php?spentityid=<?cs var:domain ?>/shibboleth&source=yahoo&RelayState=<?cs var:base ?>index.php">Login with Yahoo</a>
											</li>	
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
	