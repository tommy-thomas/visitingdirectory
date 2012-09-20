		<div id="footer-wrapper">
			<div class="container_12">
				<div id="footer" class="">
					<div class="grid_3">
						<p>&copy; 2012 <a href="http://www.uchicago.edu/">The University of Chicago</a></p>
						 <div class="social ">

						</div>
					</div>
					<div class="grid_3">
						<div class="view-footer">
							<div class="field-content">5801 S. Ellis Ave.</div>
							<div class="field-content">Chicago, IL 60637</div>
							<div class="field-content">United States</div>
						</div>
					</div>
				</div>
<!-- /#footer -->
			</div>
		</div>
<!-- /#footer-wrapper -->

<script type="text/javascript" src="https://webresources.uchicago.edu/js/jquery-1.4.4.min.js"></script>
<script src="https://webresources.uchicago.edu/js/jquery-ui-1.8.11.custom.js"></script>	
<script src="j/jquery.dataTables.min.js"></script>	
 <!-- Insert all js calls here  (jquery is included in the footer inc) -->
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
			sOut +='<td width="25%" style="align:left;">&nbsp;</td>';
			sOut +='</tr>';
			sOut +='<tr>';
			sOut +='<td>'+data[7]+'</td>';
			sOut +='<td>&nbsp;</td>';
			sOut +='<td>'+data[1];
			if( data[2] != null && data[2] != "")
			{
				sOut +='<br />'+data[2];
			}
			if( data[3] != null && data[3] != "")
			{
				sOut +='<br />'+data[3];
			}
			sOut +='</td>';
			sOut +='<td>&nbsp;</td>';
			sOut +='</tr>';
			sOut +='<tr>';
			sOut +='<td>'+data[8]+'</td>';
			sOut +='<td>&nbsp;</td>';
             sOut +='<td>';
            if( data[4] != null && data[4] != "")
            {
                sOut +=data[4]+', ';
            }
            sOut +=data[5] +'<br />'+data[6] +'</td>';
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
				"iDisplayLength": 50,
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
            $('#oButton').live('click',function(){
			  $('.row-view').each(function(){
				var aData = $(this).find('.infodiv').text();
				if( !oTable.fnIsOpen(this) )				{
					oTable.fnOpen( this,   fnFormatDetails ( aData ) , "info_row" );
				}	
			  });								   
		  });
			$('#cButton').live('click',function(){
			  $('.row-view').each(function(){
				var aData = $(this).find('.infodiv').text();
				if( oTable.fnIsOpen(this) )
				{		
					 oTable.fnClose( this );
				}
			  });								   
		  });

		});
		</script>