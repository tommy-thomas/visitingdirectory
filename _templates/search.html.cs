 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title>University of Chicago Data | The University of Chicago</title>
	
<!--//	<?cs linclude:"ssi/head.shtml" ?> //-->		
	</head>
	<body>	
	<h1>Visiting Committees Directory</h1>
	<p>You may browse the direcotry by committee or search for a committee member by name.</p>
	<h2>Browse by Committee</h2>
	<form action="results.php" method="post" name="by_committee">
	  <select name="committee" size="1">
	   <option selected>-- Select -- </option>
	  	<?cs each:c=Committee  ?>		     
			<option value="<?cs var:c.COMMITTEE_CODE ?>"><?cs var:c.SHORT_DESC ?></option>     	      			
		<?cs /each ?>
	  </select><br /><br />
	  <input type="submit" name="search_by_committee" value="Go" />
	  </form>
	  <h1>Search by Name</h1>
	  <form action="results.php" method="post" name="by_name">
	  <label for="f_name">First Name</label><br />
	  <input name="f_name" type="text" /><br />
	  <label for="l_name">Last Name</label><br />
	  <input name="l_name" type="text" /><br />
	<input type="submit" name="search_by_name" value="Search" />
	</form>
	</body>
</html>
