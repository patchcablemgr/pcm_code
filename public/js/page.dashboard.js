$( document ).ready(function() {
	function sendAjax(options) {
		var options = {
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			type: options.method || 'GET',
			dataType: 'json',
			success: options.success || function () {},
			error: options.error || function () {},
			url: options.url,
			data: options.data
		};
		$.ajax(options);
	};
	
	// Set header for JQuery to use in API calls
	//$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	
	// Test POST
	var ajaxOpts = {
		method: 'POST',
		dataType: 'json',
		success: function(){console.log('Debug (POST): success')},
		error: function(){console.log('Debug (POST): success')},
		url: '/api/user'
	};
	sendAjax(ajaxOpts);
	/*
	var postUser = $.post( "/api/user", function() {
		console.log('Debug (POST): success');
	})
	.done(function() {
		console.log('Debug (POST): second success');
	})
	.fail(function() {
		console.log('Debug (POST): error');
	})
	.always(function() {
		console.log('Debug (POST): finished');
	});
	*/
	
	$('.chartCblUtil').each(function(){
		var myChartTitle = $(this).data('mediaName');
		var myChart = new Chart(this, {
			type: 'doughnut',
			data: {
				labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
				datasets: [{
					label: '# of Votes',
					data: [12, 19, 3, 5, 2, 3],
					backgroundColor: [
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)'
					],
					borderColor: [
						'rgba(255, 99, 132, 1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)'
					],
					borderWidth: 1
				}]
			},
			options: {
				plugins: {
					title: {
						display: true,
						text: myChartTitle
					}
				}
			}
		});
	});
});