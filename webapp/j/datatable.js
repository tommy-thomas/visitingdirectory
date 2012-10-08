	/* Formating function for row details */
		function fnFormatDetails ( aData , fData)
		{
			var data = aData.split(":");
			var f_data = fData.split(":");
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
            if( data[5] != null && data[5] != "")
            {
            	sOut +=data[5] ;
            }
            if( data[6] != null && data[6] != "")
            {
            	sOut +='<br />'+data[6];
            }
            if( f_data[0] != null && f_data[0] != "")
            {
                sOut += f_data[0];
            }
            if( f_data[1] != null && f_data[1] != "")
            {
                sOut +=', ' + f_data[1];
            }
            sOut +='</td>';
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
							return "<a href=\"#\" class=\"handle\">" + oObj.aData[0] + "</a>";
						},
						"aTargets" : [0]
					}
				],
				"aaSorting": [[ 0, 'asc' ] , [1, 'asc']]
			});
			$('#results tbody td a.handle').live('click' , function(){
				var nTr = $(this).parents('tr')[0];
				var aData = $(this).find('.infodiv').text();
				var fData = $(this).find('.infodiv_foreign').text();
				if( oTable.fnIsOpen(nTr) )
				{		
					 oTable.fnClose( nTr );
					 return false;
				}
				else
				{
					oTable.fnOpen( nTr,   fnFormatDetails ( aData , fData ) , "info_row" );
					return false;
				}	
			 });
            $('#oButton').live('click',function(){
			  $('.row-view').each(function(){
				var aData = $(this).find('.infodiv').text();
				var fData = $(this).find('.infodiv_foreign').text();
				if( !oTable.fnIsOpen(this) )				{
					oTable.fnOpen( this,   fnFormatDetails ( aData , fData ) , "info_row" );
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