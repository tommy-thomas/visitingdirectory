 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title>University of Chicago Data | The University of Chicago</title>
	
<!--//	<?cs linclude:"ssi/head.shtml" ?> //-->		
	</head>
	<body>
	
<h1>Visiting Committees Directory</h1>
<h2>Member Information</h2>
<p>Didn't find what you were looking for? <a href="search.php">Start a new search.</a></p>
  <?cs each:m=CommitteeMember ?>
	<p><strong><?cs var:m.FirstName ?> <?cs var:m.MiddleName ?><?cs var:m.LastName ?></strong><br />
	<?cs var:m.DegreeInfo ?><br />
	<?cs var:m.Email ?><br />
	Phone: <?cs var:m.PhoneAreaCode ?> <?cs var:m.PhoneNumber ?><br />
	<?cs var:m.StreetOne ?><br />
	<?cs var:m.City ?>, <?cs var:m.State ?> <?cs var:m.Zip ?><br />
	<strong>Committees:</strong>
	</p>
   <?cs /each ?>
	</body>
</html>
