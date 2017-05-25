//these variables will hold the templates that will be rendered by click events on the main page

/*
var TemplateLoader = {
  _cache: {},
  get: function(path){
      if (this._cache[path]) {
	    return this._cache[path]
      }
	  return this._fetch(path)
  },
  _fetch: function(path){
    this._cache[path] = $.get(path)
    return this._cache[path]
  }
}



let cached_fetch = _.memoize(function fetch (path) { return $.get(path) })

cached_fetch('/templates/blah').then(t => Mustache.render(t))
*/

	var create_employee;
	var listof_positions;
	var add_department;
	var load_contextual_emps;
	var phone_book;
	var assign_shift;
	var phone_number;
	var workweek_datatable;
	var update_status;
	var required_on_shift;
	var shft_swap_request;
	var swaps_table;
	var accept_swap;
	var finalize_swap;
	var new_pass;

	$.ajax({
		url: "processing.php",
		data: "load_contextual_emps",
		dataType: "json",
		success: function(dat){
			load_contextual_emps = dat;
		}
	});

	$.ajax({
		url: "processing.php",
		data: "load_contextual_emps",
		dataType: "json",
		success: function(dat){
			phone_number = dat;
		}
	});

	$.ajax({
		url: "processing.php",
		data: "listof_positions",
		dataType: "json",
		success: function(dat){
		console.log(dat);
			listof_positions = dat;
		}
	});


	$.ajax({
		url: "Mustache/Mustache/views/partials/new_pass.mustache",
		success: function(dat){
			new_pass = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/finalize_swap.mustache",
		success: function(dat){
			finalize_swap = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/accept_swap.mustache",
		success: function(dat){
			accept_swap = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/swaps_table.mustache",
		success: function(dat){
			swaps_table = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/shift_swap_request.mustache",
		success: function(dat){
			shift_swap_request = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/required_on_shift.mustache",
		success: function(dat){
			required_on_shift = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/update_status.mustache",
		success: function(dat){
			update_status = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/workweek_datatable.mustache",
		success: function(dat){
			workweek_datatable = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/assign_shift.mustache",
		success: function(dat){
			assign_shift = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/phone_book.mustache",
		success: function(dat){
			phone_book = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/create_employee.mustache",
		success: function(dat){
			create_employee = dat;
		}
	});

	$.ajax({
		url: "Mustache/Mustache/views/partials/add_department.mustache",
		success: function(dat){
			add_department = dat;
		}
	});
