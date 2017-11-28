$(document).ready(function(){

	// disable input before data finish loading
	$('#progcode').prop('disabled', true);
	$('.coursecode').prop('disabled', true);
	$('#fetch').prop('disabled', true);

	// initialize Select2
	$(function(){
		$('#progcode,.coursecode').select2();
	});

	$(document).on('change','.coursecode',function(){
		$('.coursecode').select2("destroy"); // destroy Select2 first before clone
    	$(this).clone().appendTo('#cloneTarget').after('<br>'); // clone
		$('select:last').focus();
		$('.coursecode').select2(); // re-initialize 
    });

	// populate select box
	$(function(){	

		// programmes list
		$.getJSON("./api.php?option=listprogrammes", function(data){
			var programmes = "";
	        $.each(data, function(i, obj){
	        	var options= "";
	        	programmes += "<option value='"+obj.url+"'>"+obj.code+"</option>";
	        });

	        // append to select box
	        $('#progcode').append(programmes);
	    })
	    .done(function() {
			// done
			toastr.success('Programmes list fetched.')
			$('#progcode').prop('disabled', false);
		})
		.fail(function() {
			// failed
			toastr.error('Error fetching programmes list.')
		});

		// courses list
		$.getJSON("./api.php?option=listcourses", function(data){
			var courses = "";
	        $.each(data, function(i, obj){
	        	var options= "";
	        	courses += "<option value='"+obj+"'>"+obj+"</option>";
	        });

	        // append to select box
	        $('.coursecode').append(courses);
	    })
	    .done(function() {
			// done
			toastr.success('Courses list fetched.')
			$('.coursecode').prop('disabled', false);
			$('#fetch').prop('disabled', false);
		})
		.fail(function() {
			// failed
			toastr.error('Error fetching courses list.')
		});

	});	

	// fetch timetable
	$('#fetch').click(function(){

		var param = $('#inputForm').serialize();

		$('#progcode').prop('disabled', true);
		$('.coursecode').prop('disabled', true);
		$('#fetch').prop('disabled', true);

		// get timetable
		$.getJSON("./api.php?option=timetable&"+param, function(data){
	        // append data
	        var results = "";
	        for(var i=0;i<data.length;i++)
	        {
	        	var subject = "Subject: "+data[i]['subject'];
	        	var details = "<br>Week: "+data[i]['details']['week'] +"<br>Date: "+data[i]['details']['date'] +"<br>Time: "+data[i]['details']['time'];

	        	results += subject+details+"<br><br>";
	        }
	        $('#timetable').append("<h3>Timetable:</h3>"+results);
	    })
	    .done(function() {
			// done
			toastr.success('Timetable fetched.')
			$('#progcode').prop('disabled', false);
			$('.coursecode').prop('disabled', false);
			$('#fetch').prop('disabled', false);
		})
		.fail(function() {
			// failed
			toastr.error('Error fetching timetable.')
			$('#progcode').prop('disabled', false);
			$('.coursecode').prop('disabled', false);
			$('#fetch').prop('disabled', false);
		});
	});

});