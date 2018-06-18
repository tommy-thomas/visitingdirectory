	/* Formating function for row details */

		function getTextNodeValue( element, index, array ){
			return ( element != null) ? (document.createTextNode( element )).nodeValue : "";
		}
		
		function fnFormatDetails ( aData , fData)
		{
			var raw_data = aData.split(":");
			var raw_f_data = fData.split(":");
			var data = $.map(raw_data, getTextNodeValue);
			var f_data = $.map(raw_f_data,getTextNodeValue);
			var sOut = '<table width="100%" border="0" cellspacing="3" cellpadding="3" style="margin:0;">';
			sOut +='<tr>';
			sOut +='<td width="25%" style="align:left;">'+data[0]+'</td>';
			sOut +='<td width="25%" style="align:left;">&nbsp;</td>';
			sOut +='<td width="25%" style="align:left;"><em>Mailing Address:</em></td>';
			sOut +='<td width="25%" style="align:left;">&nbsp;</td>';
			sOut +='</tr>';
			sOut +='<tr>';
			sOut +='<td>'+data[5]+'</td>';
			sOut +='<td>&nbsp;</td>';
			sOut +='<td>'+data[1] +'</td>';
			sOut +='</td>';
			sOut +='<td>&nbsp;</td>';
			sOut +='</tr>';
			sOut +='<tr>';
            sOut +='<td>'+data[6]+'</td>';
            sOut +='<td>&nbsp;</td><td>';
            sOut += ( data[2] != null && data[2] != "") ? data[2] : "&nbsp;";
            sOut += ( data[3] != null && data[3] != "") ? ", " + data[3] : "&nbsp;";
            sOut += ( data[4] != null && data[4] != "") ? "<br />" +  data[4] : "&nbsp;";
            sOut += "</td>";
            sOut +='<td>&nbsp;</td></tr>';
             sOut +='<tr><td>';
			sOut +='</table>';
            console.log( data );
			console.log( f_data );
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
			  $('table#results').addClass('expanded');
			  $('.row-view').each(function(){
				var aData = $(this).find('.infodiv').text();
				var fData = $(this).find('.infodiv_foreign').text();
				if( !oTable.fnIsOpen(this) )				{
					oTable.fnOpen( this,   fnFormatDetails ( aData , fData ) , "info_row" );
				}	
			  });								   
		  });
			$('#cButton').live('click',function(){
			  $('table#results').removeClass('expanded');
			  $('.row-view').each(function(){
				var aData = $(this).find('.infodiv').text();
				if( oTable.fnIsOpen(this) )
				{		
					 oTable.fnClose( this );
				}
			  });								   
		  });

		});
