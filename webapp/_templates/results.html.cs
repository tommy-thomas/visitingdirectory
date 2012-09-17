<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
	<title>University of Chicago Data | The University of Chicago</title>
	<?cs include:"inc/head.html.cs" ?>  
	<style type="text/css" title="currentStyle">
		@import "c/demo_table_jui.css";
	</style>
	<link href="https://webresources.uchicago.edu/css/jquery-ui-1.8.11.custom.css" rel="stylesheet" type="text/css" />
  </head>
  <body class="two-col-temp">
	<div id="page-wrapper">
	  <div id="page">
		
		<?cs include:"inc/header.html.cs" ?>
		
		<div id="main-wrapper" class="">
		  <div id="main" class=" container_12">
			<div id="content" class="column grid_9 push_3 ">
			
			  <h1 id="page-title">Visiting Committees Directory</h1>
			  <div id="bottomrow" class="">
				<div class="grid_9 alpha">
					<div class="content region-content">
					<?cs if:ShowCommiteeResults ?>
						<h2><?cs var:Committee ?></h2>
						<p>Click on a member's last name to see additional information. You may sort by clicking the first or last name headings. <em>*Denotes a Life Member.</em></p>
						<p>Didn't find what you were looking for? <a href="search.php">Browse again or start a new search.</a></p>
						<p><strong><?cs var:Chairman ?></strong></p>  
						<table border="0" cellspacing="3" cellpadding="3" class="display" id="results">
							<thead>
								<tr>
									<th>Last Name</th>
									<th>First Name</th>
									<th>Email</th>
									<th>Phone</th>
								</tr>
							</thead>
							<tbody>
							<?cs each:m=CommitteeMember ?>
								<tr>
									<td><?cs var:m.LastName ?>
										<div class="infodiv" style="display:none;"><?cs var:m.DegreeInfo ?>:<?cs var:m.StreetOne ?>:<?cs var:m.City ?>:<?cs var:m.State ?>:<?cs var:m.Zip ?>:<?cs var:m.JobTitle ?>:<?cs var:m.EmployerName ?></div>
									</td>
									<td><?cs var:m.FirstName ?> <?cs var:m.MiddleName ?></td>
									<td><?cs var:m.Email ?></td>
									<td><?cs var:m.PhoneAreaCode ?> - <?cs var:m.PhoneNumber ?></td>
								</tr>
							<?cs /each ?>
							</tbody>
						</table>
				  <?cs /if ?>
				  <?cs if:ShowSearchResults ?>
				  <h2>Name Search Results</h2>
				  <p><strong><?cs var:count ?> results found.</strong> Click on a member's name to see additional information.</p>
				  <p>Didn't find what you were looking for? <a href="search.php">Start a new search.</a></p>
				  <table border="1" cellspacing="3" cellpadding="3" class="display" id="search_resuls">
					  <thead>
						  <tr>
							  <th width="50%">Name</th>
							  <th>Committees</th>      
						  </tr>
					  </thead>
					  <tbody>
					  <?cs each:m=CommitteeMember ?>
						  <tr>
							  <td><a href="member.php?id_number=<?cs var:m.IdNumber ?>"><?cs var:m.LastName ?>, <?cs var:m.FirstName ?> <?cs var:m.MiddleName ?></a></td>
							  <td><?cs var:m.CommitteesDisplay ?></td>
						  </tr>
					  <?cs /each ?>
					  </tbody>
				  </table>
				  <?cs /if ?>
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
		
  </body>
</html>
