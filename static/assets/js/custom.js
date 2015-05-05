	$(function() {
	    $('#my-select').multiSelect();
	    $('#callbacks').multiSelect({
	      afterSelect: function(values){
	        alert("Select value: "+values);
	      },
	      afterDeselect: function(values){
	        alert("Deselect value: "+values);
	      }
	    });
	});
	
	$(document).ready(function(){ 
        $("input[name='population']").change(function() {
            var student = $(this).val();
            $(".student").hide();      
            $("#"+student).show(function(){
                $(this).html($(this).html());
            });
        });
    });
	
	function fnShowHide( iCol , table){
	    var oTable = $(table).dataTable(); 
	    var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
	    oTable.fnSetColumnVis( iCol, bVis ? false : true );
	}
	
	/*
	 * TableTools Bootstrap compatibility
	 * Required TableTools 2.1+
	 */
	if ( $.fn.DataTable.TableTools ) {
		// Set the classes that TableTools uses to something suitable for Bootstrap
		$.extend( true, $.fn.DataTable.TableTools.classes, {
			"container": "DTTT btn-group",
			"buttons": {
				"normal": "btn",
				"disabled": "disabled"
			},
			"collection": {
				"container": "DTTT_dropdown dropdown-menu",
				"buttons": {
					"normal": "",
					"disabled": "disabled"
				}
			},
			"print": {
				"info": "DTTT_print_info modal"
			},
			"select": {
				"row": "active"
			}
		} );
	
		// Have the collection use a bootstrap compatible dropdown
		$.extend( true, $.fn.DataTable.TableTools.DEFAULTS.oTags, {
			"collection": {
				"container": "ul",
				"button": "li",
				"liner": "a"
			}
		} );
	}

	$(function() {
		
		//////////     DATA TABLE  COLUMN TOGGLE    //////////
		$('[data-table="table-toggle-column"]').each(function(i) {
				var data=$(this).data(), 
				table=$(this).data("table-target"), 
				dropdown=$(this).parent().find(".dropdown-menu"),
				col=new Array;
				$(table).find("thead th").each(function(i) {
				 		$("<li><a  class='toggle-column' href='javascript:void(0)' onclick=fnShowHide("+i+",'"+table+"') ><i class='fa fa-check'></i> "+$(this).text()+"</a></li>").appendTo(dropdown);
				});
		});

		//////////     COLUMN  TOGGLE     //////////
		 $("a.toggle-column").on('click',function(){
				$(this).toggleClass( "toggle-column-hide" );  				
				$(this).find('.fa').toggleClass( "fa-times" );  			
		});

		// Call dataTable in this page only
		$('#table-example').dataTable( {
	        "sDom": '<"clear">lTfrtip',
	        "aaSorting": [],
	        //""sDom": '<"clear">lTfrtip',
	        "oTableTools": {
	        	"aButtons": [
				    {
				  	  "sExtends": "copy",
				      "sButtonClass": "DTTT_button",
					},
					{
					  "sAction": "flash_save",
				  	  "sExtends": "csv",
				      "sButtonClass": "DTTT_button",
				      "sFieldBoundary": '"',
					  "sFieldSeperator": ",",
					},
					{
				  	  "sExtends": "xls",
				      "sButtonClass": "DTTT_button",
					},
					{
				  	  "sExtends": "pdf",
				      "sButtonClass": "DTTT_button",
					}
				  ],
	            "sSwfPath": commonPath + "static/assets/plugins/datable/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
	        }
		} );
		$('table[data-provide="data-table"]').dataTable();
	});