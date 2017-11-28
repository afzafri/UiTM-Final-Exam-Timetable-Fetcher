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
	    })
	    .done(function() {
			// done
		})
		.fail(function() {
			// failed
		});

	});	

});