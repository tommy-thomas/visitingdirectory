<div id="leftnav">
	<div class="content">
		<ul class="menu">
			<li class="first"><a href="http://vc.uchicago.edu/">Home</a></li>
			<li class=""><a href="https://vc.uchicago.edu/page/about-committees-and-councils">About the VCs</a></li>
			<li class=""><a href="https://vc.uchicago.edu/page/access-and-privileges">Member Access and Privileges</a></li>
			<li class=""><a href="https://vc.uchicago.edu/page/administrative-staff-contacts">Administrative Staff Contacts</a></li>
			<li class=""><a href="/index.php">Secure VC Directory</a></li>
			<li class=""><a href="https://vc.uchicago.edu/page/committee-membership" class="active">Committee Membership</a>
				<ul class="menu clearfix">
					<?cs each:c=Committee ?>
					<li class="leaf"><a href="<?cs var:base ?>visiting/committee.php?c=<?cs var:c.COMMITTEE_CODE ?>"><?cs var:c.FULL_DESC ?></a></li>
					<?cs /each ?>					
				</ul>	
			</li>
			<li class=""><a href="https://vc.uchicago.edu/page/visit-campus">Visit Campus</a></li>
			<li class=""><a href="https://vc.uchicago.edu/contact-us">Contact Us</a></li>
			<?cs if:LoggedIn ?><li><a href="https://shibboleth2.uchicago.edu/idp/logout.html">Log Out</a></li><?cs /if ?>
            
		</ul>
	</div>
</div>