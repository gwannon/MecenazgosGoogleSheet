    var iso = jQuery('.grid').isotope({
      itemSelector: '.element-item',
      layoutMode: 'fitRows',
      getSortData: {
        name: '.name',
        days: '.days parseInt',
        daysasc: '.days parseInt',
        oficialdate: '.oficialdate',
        oficialdateasc: '.oficialdate'
      },
      sortAscending: {
        name: true,
        days: false,
        daysasc: true,
        oficialdate: false,
        oficialdateasc: true
      }
    });

    var filterClass = '';
    jQuery('select:not(select#select-ordenar)').on( 'change', function() {
      filterClass = '';
      jQuery('select:not(select#select-ordenar)').each(function(){
        if(jQuery(this).val() != '') filterClass = filterClass + '.' + jQuery(this).val();
      });
      iso.isotope({ filter: filterClass });
    });

     jQuery('#select-ordenar').on( 'change', function() {
      iso.isotope({
        sortBy: jQuery(this).val()
      });
    });

	$('.accesible').on('click', function(e) {
		e.preventDefault();
		$('body').toggleClass("acc");
	});