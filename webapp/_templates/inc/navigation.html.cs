<div id="leftnav">
	<div class="content">
		<ul class="menu ">
			<li class="first"><a href="http://trustees.uchicago.edu/" class="active">Board of Trustees</a></li>
			<li class=""><a href="search.php">Directory Search</a></li>
			<li class=""><a href="./visiting.php">Visiting Committees</a>
				<ul class="menu clearfix">
					<?cs each:c=Committee ?>
					<li class="leaf"><a href="committee.php?c=<?cs var:c.COMMITTEE_CODE ?>"><?cs var:c.FULL_DESC ?></a></li>
					<?cs /each ?>					
				</ul>	
			</li>
			<?cs if:LoggedIn ?><li><a href="https://shibboleth2.uchicago.edu/idp/logout.html">Log Out</a></li><?cs /if ?>
            
		</ul>
	</div>
</div>