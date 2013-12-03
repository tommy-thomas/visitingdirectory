<div class="column-width-3 navbar" role="navigation">
	<div class="navbar-inner">
		<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse" title="Toggle Main Navigation">
        	<span class="icon-bar"></span>
        	<span class="icon-bar"></span>
        	<span class="icon-bar"></span>
      	</button>
		<div id="nav-collapse" class=" collapse nav-collapse" role="navigation">
			<div id="leftnav" role="navigation">
				<div class="content">
					<ul class="menu ">
						<li class="menu__item is-leaf first leaf">
							<a href="http://vc.uchicago.edu/" class="active">Visiting Committees and Councils</a>
						</li>
						<li class="">
							<a href="https://vc.uchicago.edu/page/committee-membership">Committee Membership</a>
							<ul class="menu">
								<li>
									<a href="./search.php">Directory Search</a>
								</li>
							</ul>				
						</li>
						<?cs if:LoggedIn ?>
							<li>
								<a href="https://shibboleth2.uchicago.edu/idp/logout.html">Log Out</a>
							</li>
						<?cs /if ?>
					</ul>
				</div>
			</div>
			<?cs include:"inc/related-links.html.cs" ?>
		</div>
	</div>
</div>	