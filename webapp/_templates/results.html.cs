 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Board of Trusteees | The University of Chicago</title>
<style type="text/css" title="currentStyle">
	@import "c/demo_table_jui.css";
</style>
<link href="https://webresources.uchicago.edu/css/jquery-ui-1.8.11.custom.css" rel="stylesheet" type="text/css" />
<script src="https://webresources.uchicago.edu/js/jquery-1.4.2.min.js"></script>
<script src="./j/jquery.dataTables.min.js"></script>
<script src="https://webresources.uchicago.edu/js/jquery-ui-1.8.11.custom.js"></script>	
</head>
<body>	
<h1>Visiting Committees Directory</h1>
<?cs if:ShowCommiteeResults ?>
<h2><?cs var:Committee ?></h2>
<p>Click on a member's last name to see additional information. You may sort by clicking the first or last name headings. <em>*Denotes a Life Member.</em></p>
<p>Didn't find what you were looking for? <a href="search.php">Broswe again or start a new search.</a></p>
<p><?cs var:Chairman ?> </p>	
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
    <div class="infodiv" style="display:none;"><?cs var:m.DegreeInfo ?>:<?cs var:m.StreetOne ?>:<?cs var:m.City ?>:<?cs var:m.State ?>:<?cs var:m.Zip ?></div>
    </td>
    <td><?cs var:m.FirstName ?> <?cs var:m.MiddleName ?></td>
    <td><?cs var:m.Email ?></td>
	<td><?cs var:m.PhoneAreaCode ?> - <?cs var:m.PhoneNumber ?></td>
  </tr>
   <?cs /each ?>
  </tbody>
</table>
</body>
<script type="text/javascript" charset="utf-8">
/* Formating function for row details */
function fnFormatDetails ( aData )
{
    var data = aData.split(":");
    var sOut = '<table width="100%" border="0" cellspacing="3" cellpadding="3" style="background-color:#ccc; margin:0;">';   
    sOut +='<tr>';
    sOut +='<td width="25%" style="align:left;">'+data[0]+'</td>';
    sOut +='<td width="25%" style="align:left;">&nbsp;</td>';
    sOut +='<td width="25%" style="align:left;"><em>Mailing Address:</em></td>';
    sOut +='<td width="25%" style="align:left;"><em>Fax Number:</em></td>';
    sOut +='</tr>';
    sOut +='<tr>';
    sOut +='<td>&nbsp;</td>';
    sOut +='<td>&nbsp;</td>';
    sOut +='<td>'+data[1]+'</td>';
    sOut +='<td>&nbsp;</td>';
    sOut +='</tr>';
    sOut +='<tr>';
    sOut +='<td>&nbsp;</td>';
    sOut +='<td>&nbsp;</td>';
    sOut +='<td>'+data[2]+', '+ data[3] + data[4] +'</td>';
    sOut +='<td>&nbsp;</td>';
    sOut +='</tr>';
    sOut +='</table>';
    return sOut;
}
 
$(function() {
    /*
     * Initialise DataTables
     */
    var oTable = $('#results').dataTable(
    {   
    	"bJQueryUI": true,
    	"aoColumnDefs" : [
    		{
    			"fnRender" : function( oObj , sVal ){
    				return "<a href=\"#\">" + oObj.aData[0] + "</a>";
    			},
    			"aTargets" : [0]
    		}
    	],
    	"aaSorting": [[ 0, 'asc' ] , [1, 'asc']]
    });
    $('#results tbody td a').live('click' , function(){
		var nTr = $(this).parents('tr')[0];
		var aData = $(this).find('.infodiv').text();
		if( oTable.fnIsOpen(nTr) )
		{		
			 oTable.fnClose( nTr );
			 return false;
		}
		else
		{
			oTable.fnOpen( nTr,   fnFormatDetails ( aData ) , "info_row" );
			return false;
		}	
     });

});
</script>
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
</html>