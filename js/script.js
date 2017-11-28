$(document).ready(function(){

	//populate select box
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
	        alert("success");
	    })
	    .done(function() {
			// done
		})
		.fail(function() {
			// failed
		});

		// courses list
		$.getJSON("./api.php?option=listcourses", function(data){
			var courses = "";
	        $.each(data, function(i, obj){
	        	var options= "";
	        	courses += "<option value='"+obj+"'>"+obj+"</option>";
	        });

	        // append to select box
	        $('#coursecode').append(courses);
	        alert("success");
	    })
	    .done(function() {
			// done
		})
		.fail(function() {
			// failed
		});

	});	

	// fetch timetable
	$('#fetch').click(function(){
		var progcode = $('#progcode').val();
		var coursecode = $('#coursecode').val();
		// get timetable
		$.getJSON("./api.php?option=timetable&progcode="+progcode+"&coursecode="+coursecode, function(data){
	        // append data
	        var subject = "Subject: "+data.subject;
	        var details = "<br>Week: "+data.details['week'] +"<br>Date: "+data.details['date'] +"<br>Time: "+data.details['time'];

	        $('#timetable').append(subject+details);
	        alert("success");
	    })
	    .done(function() {
			// done
		})
		.fail(function() {
			// failed
		});
	});

});