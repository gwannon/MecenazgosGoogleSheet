		$(window).on('load', function() {
			var $grid = $('.grid').isotope({
				itemSelector: '.element-item',
				layoutMode: 'fitRows',
				/*masonry: {
					columnWidth: '.element-item'
				},*/
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

			$('#filters button').on('click', function() {
				$('html, body').animate({
					scrollTop: $('#filters').offset().top
				}, 300);
				$('#filters button.is-checked').removeClass("is-checked");
				$(this).addClass("is-checked");
				var filterValue = $(this).attr('data-filter');
				// use filterFn if matches value
				/*filterValue = filterFns[ filterValue ] || filterValue;*/
				$grid.isotope({
					filter: filterValue
				});
			});

			$('#sorts button').on('click', function() {
				$('html, body').animate({
					scrollTop: $('#filters').offset().top
				}, 300);
				$('#sorts button.is-checked').removeClass("is-checked");
				$(this).addClass("is-checked");
				var sortByValue = $(this).attr('data-sort-by');
				$grid.isotope({
					sortBy: sortByValue
				});
			});
		});

		$('.accesible').on('click', function(e) {
			e.preventDefault();
			$('body').toggleClass("acc");
		});