/*
 * For set all the panels after getting the data
 */

var show_loader = function(){
    jQuery('.main-loader').show();
};

var hide_loader = function(){
    jQuery('.main-loader').hide();
};

var validate_search_fields = function(){
    jQuery( '.search-from' ).trigger( 'blur' );
    if( jQuery( '#txt-miles' ).val() > 0 && jQuery.trim( jQuery( '#txt-zipcode' ).val() ) == ''  ){
        jQuery( '#txt-zipcode' ).focus();
        jQuery( '#txt-zipcode' ).addClass( 'err-border' );
        return false;
    } else {
        jQuery( '#txt-zipcode' ).removeClass( 'err-border' );
        return true;
    }
};

var set_panels = function () {
    jQuery.fn.togglepanels = function () {
        return this.each(function () {
            jQuery(this).addClass("ui-accordion ui-accordion-icons ui-widget ui-helper-reset")
                    .find("h3")
                    .addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom")
                    .hover(function () {
                        jQuery(this).toggleClass("ui-state-hover");
                    })
                    .prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>')
                    .click(function () {
                        jQuery(this)
                                .toggleClass("ui-accordion-header-active ui-state-active ui-state-default ui-corner-bottom")
                                .find("> .ui-icon").toggleClass("ui-icon-triangle-1-e ui-icon-triangle-1-s").end()
                                .next().slideToggle();
                                window.dispatchEvent(new Event('resize'));
                                //jQuery( '#container-broker-history' ).css( 'height', 'auto' );
                        return false;
                    })
                    .next()
                    .addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom")
                    .hide();
        });
    };
};

var init_search_accordians = function(){
    set_panels();
    jQuery(".search-wrapper").togglepanels();
    jQuery(".search-wrapper h2.ui-accordion-header:first-child").addClass('ui-accordion-header-active ui-state-active').removeClass(' ui-state-default ui-corner-bottom');
    jQuery( ".search-wrapper" ).find( "h3" ).trigger( "click" );
};

var load_data = function (year, ein) {
    show_loader();
    jQuery('.main-wrap-overview').html( '' );
    var datastring = {year: year, ein: ein, action: "display_data_yearwise"};
    var data = datastring;
    var ajaxurl = analytics.ajax_url;

    
    jQuery.ajax({
        type: "POST",
	url: analytics.ajax_url,
        data: datastring,
        datatype: 'html',
        success:function( response ){

        if ( response == 0 ) {
                return;
            }
            jQuery('.main-wrap-overview').html( response );

            /* Accordion JS */
            set_panels();

            jQuery("#accordion").togglepanels();
            jQuery("#accordion h3.ui-accordion-header:first-child").addClass('ui-accordion-header-active ui-state-active').removeClass(' ui-state-default ui-corner-bottom');
            jQuery("#accordion div.coverage-overview").show();
            /* Accordion JS */
            
            hide_loader();
        },
        timeout: 500000
    });
    
};

var scroll_to = function( hash ){
    // Using jQuery's animate() method to add smooth page scroll
    // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area

    jQuery( 'html, body' ).animate({
        scrollTop: jQuery( hash ).offset().top
    }, 500, function () {

        // Add hash (#) to URL when done scrolling (default click behavior)
        window.location.hash = hash;
    });
};

var is_calling = 0;

(function ($) {
    var result_table = jQuery( '.tbl-search-results' ).dataTable({
        "pageLength": 25,
    });
    jQuery( '.tbl-div' ).hide();

    /*
     * Event to search companies by selected criteria
     */
    //console.log( analytics_extra.show_results );
    jQuery( document ).on( 'submit', '#frm-search-analytics', function(){
        if( typeof analytics_extra != 'undefined' && typeof analytics_extra.show_results != 'undefined' && analytics_extra.show_results == 0 ){
            jQuery( '#btn-submit' ).addClass( 'btn-disabled' );
            if( validate_search_fields() ){
                return true;
            }
            return false;
        }
        if( is_calling == 0 ){
            jQuery( '#btn-submit' ).addClass( 'btn-disabled' );
            if( validate_search_fields() ){

                var data = jQuery( this ).serializeArray();
                
                
                data.push( 
                        {name: 'action', value: 'analytics_search'}
                        );
                is_calling = 1;
                
                jQuery.post( analytics.ajax_url, data, function( response ) {
                    is_calling = 0;
                    result_table.dataTable().fnDestroy();
                    var tbl_search = jQuery( '.tbl-search-results' ).find( 'tbody' );
                    tbl_search.html( '' );

                    var result = JSON.parse( response );
                    
                    if(result.data_count >= '500'){
                    	jQuery('.warning-message-over-results').css('display','block');
                    }else{
			jQuery('.warning-message-over-results').css('display','none');	
                    }

                    if( typeof result.success_flag != 'undefined' ){
                        if( result.success_flag == '1' ){
                            var tbl_rows = '';

                            if( typeof result.data != 'undefined' ){
                                var distance = 0;
                                if( typeof result.distance != 'undefined' && result.distance == 1 ){
                                    distance = 1;
                                    jQuery( '.tbl-search-results' ).find( 'thead tr .show-distance' ).remove();
                                    jQuery( '.tbl-search-results' ).find( 'thead tr ' ).append( '<th class="show-distance">Distance (Miles)</th>' );
                                } else {
                                    jQuery( '.tbl-search-results' ).find( 'thead tr .show-distance' ).remove();
                                }
                                jQuery.each( result.data, function( i, data ){
                                    var tbl_row = '<tr data-ein="' + data.SPONS_DFE_EIN + '">';
                                    
                                    tbl_row += '<td>' + data.SPONSOR_DFE_NAME + '</td>';
                                    tbl_row += '<td>' + data.INS_CARRIER_NAME_NORMALIZED + '</td>';
                                    tbl_row += '<td>' + data.INS_BROKER_NAME_NORMALIZED + '</td>';
                                    tbl_row += '<td>' + data.state_name + '</td>';
                                    tbl_row += '<td>' + data.PARTICIPANTS.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
                                    tbl_row += '<td>' + data.TOTAL_PREMIUMS.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
                                    tbl_row += '<td>' + data.BROKER_REVENUE.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
                                    if( distance == 1 ){
                                        tbl_row += '<td>' + parseFloat( data.distance ).toFixed( 2 ) + '</td>';
                                    }
                                    tbl_rows += tbl_row;
                                });

                                tbl_search.append( tbl_rows );
                                var txt_search = '';
                                var txt_state = "";
                                var txt_participants = "";
                                var txt_premium = "";
                                var txt_broker = "";
                                var txt_city = "";
                                var txt_miles = "";
                                var txt_zip = "";
                                var carrier_name = "";
                                var broker_name = "";
                                
                                
                                
                                if(jQuery( '.txt-search' ).val() != ''){
                                	var txt_search = "EIN Number :"+jQuery( '.txt-search' ).val();
                                }
                                
                                if(jQuery( '.ddl-state option:selected' ).val() != ''){
                                	var txt_state = "state :"+jQuery('.ddl-state option:selected').text();
                                }
                                
                                if(jQuery( '#txt-participants-from option:selected' ).val() != '' && jQuery( '#txt-participants-to option:selected' ).val()){
                                	var txt_participants = 'Participate '+jQuery( '#txt-participants-from option:selected' ).val()+' To '+jQuery( '#txt-participants-to option:selected' ).val();
                                }
                                
                                if(jQuery( '#txt-premium-from option:selected' ).val() != '' && jQuery( '#txt-premium-to option:selected' ).val()){
                                	var txt_premium = 'Premium '+jQuery( '#txt-premium-from option:selected' ).val()+' To '+jQuery( '#txt-premium-to option:selected' ).val();
                                }
                                
                                if(jQuery( '#txt-broker-revenue-from option:selected' ).val() != '' && jQuery( '#txt-broker-revenue-to option:selected' ).val()){
                                	var txt_broker = 'Broker Revenue '+jQuery( '#txt-broker-revenue-from' ).val()+' To '+jQuery( '#txt-broker-revenue-to' ).val();
                                }
                                                                
                                if(jQuery( '#txt-city' ).val() != ''){
                                	var txt_city = "City :"+jQuery('#txt-city').val();
                                }
                                
                                if(jQuery( '#txt-miles option:selected' ).val() != '' && jQuery( '#txt-miles option:selected' ).val() != '0'){
                                	var txt_miles = "Miles :"+jQuery('#txt-miles option:selected').val();
                                }
                                
                                if(jQuery( '#txt-zipcode' ).val() != ''){
                                	var txt_zip = "Zip Code :"+jQuery('#txt-zipcode').val();
                                }
                                
                                if(jQuery( '#txt-carrier' ).val() != ''){
                                	var carrier_name = "carrier Name :"+jQuery('#txt-carrier').val();
                                	
                                }
                                
                                if(jQuery( '#txt-broker' ).val() != ''){
                                	var broker_name = "Broker Name :"+jQuery('#txt-broker').val();
                                	
                                }
 
                                
                                var dt_options = {dom: 'lBftip',"pageLength": 25,
                                buttons: [
			            {
			            extend: 'copyHtml5',
			            text: "<img src='"+analytics.theme_url+"/assets/images/copy-icon.png'>",
			            },
			            {
			            extend: 'excelHtml5',
			            title: 'Analytics Search Excel',
			            text: "<img src='"+analytics.theme_url+"/assets/images/xls-icon.png'>",
			            messageTop: 'Search By '+txt_search+' '+txt_state+' '+txt_participants+' '+txt_premium+' '+txt_broker+' '+txt_city+' '+txt_miles+' '+txt_zip+' '+carrier_name+' '+broker_name,
			            },
			            {
			            extend: 'pdfHtml5',
			            title: 'Analytics Search PDF',
			            text : "<img src='"+analytics.theme_url+"/assets/images/pdf-icon.png'>",
			            messageTop: 'Search By '+txt_search+' '+txt_state+' '+txt_participants+' '+txt_premium+' '+txt_broker+' '+txt_city+' '+txt_miles+' '+txt_zip+' '+carrier_name+' '+broker_name,
			            },
			            {
			            extend: 'csvHtml5',
			            title: 'Analytics Search CSV',
			            text : "<img src='"+analytics.theme_url+"/assets/images/csv-icon.png'>",
			            customize: function (csv) {
							return 'Search By '+txt_search+' '+txt_state+' '+txt_participants+' '+txt_premium+' '+txt_broker+' '+txt_city+' '+txt_miles+' '+txt_zip+' '+carrier_name+' '+broker_name+"\n"+  csv;
						}
			            }
			        ]
                                };

                                if( distance == 1 ){
                                        dt_options.order = [[ 5, "asc" ]];
                                }
                                result_table.dataTable( dt_options );
                            }

                            jQuery( '.err-div' ).find( 'p' ).html( '' );

                            jQuery( '.tbl-div' ).show();
                        } else {
                            jQuery( '.err-div' ).find( 'p' ).html( result.message );
                            jQuery( '.tbl-div' ).hide();
                        }
                    } else {
                        jQuery( '.err-div' ).find( 'p' ).html( result.message );
                        jQuery( '.tbl-div' ).hide();
                    }
                    jQuery( '#search-results' ).find( '.search-section-header' ).show();
                    jQuery( '#btn-submit' ).removeClass( 'btn-disabled' );
                }).fail( function(){
                    jQuery( '#btn-submit' ).removeClass( 'btn-disabled' );
                }).always( function(){
                    jQuery( '.analytics-result' ).html( '' );
                    scroll_to( "#search-results" );
                    is_calling = 0;
                });
            } else {
                jQuery( '#btn-submit' ).removeClass( 'btn-disabled' );
                is_calling = 0;
            }
            jQuery( '#analytics-result' ).find( '.search-section-header' ).hide();
        } else {
            return false;
        }
        return false;
    });

    /*
     * Event to open the analytics page of selected row
     */
    jQuery( document ).on( 'click', '.tbl-search-results tbody tr', function(){
        var ein_no = jQuery( this ).data( 'ein' );
        
        if( typeof analytics_extra != 'undefined' && typeof analytics_extra.show_analytics != 'undefined' && analytics_extra.show_analytics == 1 ){
            var data = [
                {name: 'action', value: 'load_analytics'},
                {name: 'ein', value: ein_no}
            ];

            jQuery.post( analytics.ajax_url, data, function( response ) {
                jQuery( '.analytics-result' ).html( response );
                var year = jQuery( '#start_year' ).val();
                load_data( year, ein_no );
                scroll_to( '#analytics-result' );
            });

            jQuery( '#analytics-result' ).find( '.search-section-header' ).show();
        } else {
            var back_id = jQuery( '#hdn-back-id' ).val();
            var submit_url = jQuery( '#frm-search-analytics' ).prop( 'action' );
            var url = jQuery( '#frm-search-analytics' ).prop( 'action', submit_url + '?ein=' + ein_no + '&back=' + back_id );
            analytics_extra.show_results = 0;
            analytics_extra.show_analytics = 0;
            jQuery( '#frm-search-analytics' ).submit();
        }
    });

    /*
     * Events to validate search range
     */
    jQuery( document ).on( 'change', '.search-from', function(){
        var $parent = jQuery( this ).parents( '.search-criteria' );
        var from_val = parseInt( jQuery( this ).val() );
        var $to_val = $parent.find( '.search-to' );
        var to_val = parseInt( $to_val.val() );
        if( from_val > to_val ){
            $to_val.val( from_val );
        } else if( from_val == '' ) {
            jQuery( this ).val( '0' );
        }
    });

    jQuery( document ).on( 'change', '.search-to', function(){
        var $parent = jQuery( this ).parents( '.search-criteria' );
        var to_val = parseInt( jQuery( this ).val() );
        var $from_val = $parent.find( '.search-from' );
        var from_val = parseInt( $from_val.val() );
        if( from_val > to_val ){
            $from_val.val( to_val );
        } else if( to_val == '' ) {
            jQuery( this ).val( '0' );
        }
    });
    
    /*
     * Event to load result when year has been changed
     */
    jQuery(document).on('change', '.business_year', function () {
        var year = jQuery(this).val();
        var ein = jQuery(this).data('ein');
        load_data(year, ein);
    });
    
    init_search_accordians();
    
})(jQuery);

jQuery( window ).load( function(){
    if( typeof analytics_extra != 'undefined' && typeof analytics_extra.show_results != 'undefined' && analytics_extra.show_results == 1 && typeof searched_data != 'undefined' ){
        //jQuery( "#ddl-state" ).val( searched_data.ddl_state );
        jQuery( "#txt-participants-from" ).val( searched_data.txt_participants_from );
        jQuery( "#txt-participants-to" ).val( searched_data.txt_participants_to );
        jQuery( "#txt-premium-from" ).val( searched_data.txt_premium_from );
        jQuery( "#txt-premium-to" ).val( searched_data.txt_premium_to );
        jQuery( "#txt-broker-revenue-from" ).val( searched_data.txt_broker_revenue_from );
        jQuery( "#txt-broker-revenue-to" ).val( searched_data.txt_broker_revenue_to );
        //jQuery( "#txt-miles" ).val( searched_data.txt_miles );
        
        jQuery( ".search-wrapper-sidebar #frm-search-analytics" ).trigger( "submit" );
    }
});