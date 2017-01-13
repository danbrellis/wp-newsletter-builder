// JavaScript Document

jQuery(document).ready(function($) {
	"use strict";
	$('#acb-newsletter-addto-suggest').suggest(
		ajaxurl + '?action=newsletter_lookup', 
		{
			'minchars': 0,
			'onSelect': function process_newsletter_suggest_select(){
				var ival = $(this).val();
								
				var res = ival.replace(/\[\[\@([0-9]+)\@\]\]/, function myFunction(x){
					$('#acb-newsletter-addto').val(x.replace('[[@','').replace('@]]',''));
					return "";
					
				});
				
				$.post(ajaxurl, {'action': 'acb_newsletter_get_posturl_by_id', 'id': $('#acb-newsletter-addto').val()}, function(response) {
					if(response.status === 'success'){
						var newLink = $("<a />", {
							href : response.msg,
							target : '_blank',
							text : res
						});
						$('.acb_newsletter_addto_title code').html(newLink[0]);
						$('#acb-newsletter-adding').prop('disabled', false);
					}
					else {
						alert('Error: ' + response.msg);
					}
				});
				
				
				
				$(this).val("");
			}
			
		}
	);
	
	$('#acb-newsletter-adding').click(function(){
		var id = $('#acb-newsletter-addto').val();
		var spinner = $(this).next('span.spinner');
		spinner.css('visibility', 'visible');
		
		
		if(!id.length) {
			acbnwsltr_reset();
			return;
		}

		
		$.post(ajaxurl, 
			{
				'action': 'acb_newsletter_add_item', 
				'nwsltr_id': id, 
				'security': $("#acb_newsletter_add_item_nonce").val(), 
				'post_id': $('#post_ID').val() 
			},
			function(r) {
				alert(r);
			
				//done
				acbnwsltr_reset();
			}
		);
		
	});
	
	function acbnwsltr_reset(){
		$('#acb-newsletter-adding').prop('disabled', true);
		$('#acb_newsletter_adding_cont .spinner').css('visibility', 'hidden');
		$('#acb_newsletter_adding_cont code').html("NONE");
		$("#acb-newsletter-addto-suggest").val("");
		$("#acb-newsletter-addto").val("");
	}
	
	if(typenow === 'e_newsletter' && $('#acb_newsletter_items_sortable').length){
		//delete item if called
		if(window.location.hash) {
			var hash = window.location.hash;
			if(~hash.indexOf("newsletter-item-delete-")){
				var id_to_delete = hash.replace( /^\D+/g, '');
				var ids = $("#newsletter_item_ids").val().split(',');
				ids = $.grep(ids, function(value) {
					return value !== id_to_delete;
				});
				$("#newsletter_item_ids").val(ids.join());
				$("#newsletter-item-"+id_to_delete).remove();
			}
		}
		
		//enable drag & drop
		$( "#acb_newsletter_items_sortable" ).sortable({
			placeholder: {
        element: function(currentItem) {
            return $('<li class="sortable-placeholder menu-item-depth-0" style="height:40px;"></li>')[0];
        },
        update: function(container, p) {
            return;
        }
	    },
			update: function() {
				var item_ids;
				item_ids = $(this).sortable('toArray', {attribute: 'data-newsletteritemid'});
				$("#newsletter_item_ids").val(item_ids.join());
			},
		});
    $( "#acb_newsletter_items_sortable" ).disableSelection();
		
		$('.newsletter-item-delete').on('click', function(e){
			e.preventDefault();
			$(this).closest('li').remove();
			var item_ids;
			item_ids = $( "#acb_newsletter_items_sortable" ).sortable('toArray', {attribute: 'data-newsletteritemid'});
			$("#newsletter_item_ids").val(item_ids.join());
		});
	}
	
});