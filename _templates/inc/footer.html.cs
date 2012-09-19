		<div id="footer-wrapper">
			<div class="container_12">
				<div id="footer" class="">
					<div class="grid_3">
						<p>&copy; 2012 <a href="http://www.uchicago.edu/">The University of Chicago</a></p>
						<div class="social ">
							<div id="block-views-footer-socialmedia" class=" block block-views">
								<div class="content">
									<div class="view-footer">
										<div class="field-content">
											<a href="http://www.facebook.com/uchicago" class="facebook">Follow us on Facebook</a>
										</div>
										<div class="field-content">
											<a href="http://www.twitter.com/uchicago" class="twitter">Follow us on Twitter</a>
										</div>
										<div class="field-content">
											<a href="http://www.youtube.com/uchicago" class="youtube">Follow us on YouTube</a>
										</div>
										<div class="field-content">
											<a href="http://www.flickr.com/groups/uofc" class="flickr">Follow us on Flickr</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="grid_3">
						<div class="view-footer">
							<div class="field-content">123 West Way</div>
							<div class="field-content">Second Floor</div>
							<div class="field-content">Chicago, IL 60637</div>
							<div class="field-content">United States</div>
						</div>
					</div>
					<!-- <div class="grid_3">
						<div class="view-footer">
							<div class="field-content">
								<a href="http://www.uchicago.edu">Lorem Ipsum</a>
							</div>
							<div class="field-content">
								<a href="http://www.uchicago.edu">Dolor Sit Amet</a>
							</div>
							<div class="field-content">
								<a href="http://www.uchicago.edu">Adipiscing Sed Ligula</a>
							</div>
							<div class="field-content">
								<a href="http://www.uchicago.edu">Quis Vulputate Nunc Porta</a>
							</div>
						</div>
					</div>
					<div class="grid_3">
						<div class="view-footer">
							<div class="field-content">
								<a href="http://www.uchicago.edu">Blandit Massa Mattis Luctus</a>
							</div>
							<div class="field-content">
								<a href="http://www.uchicago.edu">Malesuada Felis Quis</a>
							</div>
							<div class="field-content">
								<a href="http://www.uchicago.edu">Nulla Sodales Massa</a>
							</div>
							<div class="field-content">
								<a href="http://www.uchicago.edu">Pellentesque Ultricies</a>
							</div>
						</div>
					</div> -->
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
			sOut +='<td>'+data[5]+'</td>';
			sOut +='<td>&nbsp;</td>';
			sOut +='<td>'+data[1]+'</td>';
			sOut +='<td>&nbsp;</td>';
			sOut +='</tr>';
			sOut +='<tr>';
			sOut +='<td>'+data[6]+'</td>';
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
                 return false;									   
		  });
			$('#cButton').live('click',function(){
			  $('.row-view').each(function(){
				var aData = $(this).find('.infodiv').text();
				if( oTable.fnIsOpen(this) )
				{		
					 oTable.fnClose( this );
				}                
			  });
                 return false;									   
		  });

		});
		</script>