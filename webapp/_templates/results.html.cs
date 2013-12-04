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
	<style type="text/css" title="currentStyle">
		@import "c/demo_table_jui.css";
	</style>
	
</head>  
<body>
	<?cs include:"inc/header.html.cs" ?>
	<div class="container">
		<h1 class="page-title">Secure Directory - beta</h1>
			<div class="row">
				<div class="span9 pull-right">
					<div class="content region-content">
						<?cs if:ShowCommiteeResults ?>
						
						<p>Didn't find what you were looking for? <a href="search.php">Browse again or start a new search.</a></p>
						
						<h2 id="cmtname"><?cs var:Committee ?></h2>
						
						<p>
							<ul>
								<li><strong>Click on a member's last name to see additional information.</strong></li>
								<li>You may sort by clicking the table's headings headings.</li>
								<li>Data incorrect or incomplete? <a href="mailto:spaepen@uchicago.edu?subject=Update for Visiting Committee Member">Contact us with an update.</a></li>
							</ul>
							<em>*Denotes a Life Member.</em>
						</p>
						<p class="hiddenscreen">Please Note: Member data is not available for printing at this time. Please contact Alumni Relations &amp; Development if you need a copy of this information.</p>
						<div class="hideprint" style="float:left; margin-left:5px;">
							<h2><?cs var:Chairman ?></h2>
						</div>
                        <div class="hideprint" style="float:right; margin-right:5px;">
                        	<a href="#" id="oButton">+ expand all</a> / <a href="#" id="cButton">- collapse all</a>
                        </div>  
                        <div class="memberdata">
							<table border="0" cellspacing="3" cellpadding="3" class="display table table-striped" id="results">
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
									<tr class="row-view">
										<td><strong><?cs var:m.LastName ?></strong>
											<div class="infodiv" style="display:none;"><?cs var:m.DegreeInfo ?>:<?cs var:m.StreetOne ?>:<?cs var:m.StreetTwo ?>:<?cs var:m.StreetThree ?>:<?cs var:m.City ?>:<?cs var:m.State ?>:<?cs var:m.Zip ?>:<?cs var:m.JobTitle ?>:<?cs var:m.EmployerName ?></div>
											<div class="infodiv_foreign" style="display:none;"><?cs var:m.ForeignCityZip ?>:<?cs var:m.CountryCode ?></div>										
										</td>
										<td><?cs var:m.FirstName ?> <?cs var:m.MiddleName ?></td>
										<td><a href="mailto:<?cs var:m.Email ?>"><?cs var:m.Email ?></a></td>
										<td><a href="tel:<?cs var:m.PhoneNumber ?>"><?cs var:m.PhoneNumber ?></a></td>
									</tr>
								<?cs /each ?>
								</tbody>
							</table>
						</div>
						<?cs /if ?>

						<?cs if:ShowSearchResults ?>
					  	<h2>Name Search Results</h2>
						<p><strong><?cs var:count ?> results found.</strong> Click on a member's name to see additional information.</p>
						<p>Didn't find what you were looking for? <a href="search.php">Start a new search.</a></p>
						<p class="hiddenscreen">Please Note: Member data is not available for printing at this time. Please contact Alumni Relations &amp; Development if you need a copy of this information.</p>
						<div class="memberdata">
							<table border="0" cellspacing="3" cellpadding="3" class="display table table-striped" id="search_resuls">
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
						</div>
						<?cs /if ?>
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
	