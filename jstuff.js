$(document).ready(function(){

function clearModalTemplate(){
	//clear modal title
	$('.modal-title').html("");
	//clear the modal body
	$('.modal-body').html("");
}

var isAdmin = "";

$.ajax({
	url: "processing.php",
	dataType: "json",
	data: 'getAdmin',
	success: function(dat){
		isAdmin = dat;
	}
});

var DATEobj = new Date();

//http://zerosixthree.se/snippets/get-week-of-the-year-with-jquery/
Date.prototype.getWeek = function() {
	var onejan = new Date(this.getFullYear(),0,1);
	return Math.ceil((((this - onejan) / 86400000) + onejan.getDay()+1)/7);
}
	//initialize datatable
	$("#employee_shift_data").DataTable({ 'order' : [[0, 'asc']]});

	$("#swap_tab").on("click", function(){
		$(".shifts_divs").hide();
		var TPL = [];

		$.ajax({
			url: "processing",
			dataType: "json",
			data: 'getSwapRecords',
			success: function(dat){
				TPL['employeeData'] = dat;
				$("#employee_shift_data").remove();
				$("#swap_div").html(Mustache.render(swaps_table, TPL));
				$("#swap_div").show();
				$("#employee_shift_data").DataTable({ 'order' : [[0, 'asc']]});
			}
		});
	});

	//SHIFTS TABS
	$(".shifts_tabs").on("click", function(e){
		e.preventDefault();
		//hide all divs containing datatables
		$(".shifts_divs").hide();
		$("#swap_div").hide();
		//show corresponding div
		var theId = $(this).attr("id");
		var TPL = [];

		//find if admin or not
		$.ajax({
			url: "processing.php",
			dataType: "json",
			data: 'getAdmin',
			success: function(dat){
				TPL['isAdmin'] = dat;
				//console.log("Is Supervisor: " + dat);
			}
		});

		//get relevent data
		 $.ajax({
			 url: "processing.php",
			 dataType: 'json',
			 data: {"context" : theId },
			 success: function(dat){

			 	TPL['listofemployees'] = dat;
				TPL['dataGroup'] = theId;
				TPL['pastModal'] = theId == "past_shifts" ? true : false;
			 	$("#employee_shift_data").remove();
			 	$("#"+ theId +"_div").html(Mustache.render(workweek_datatable, TPL));

			 	//reinitialize datatable
			 	$("#employee_shift_data").DataTable({ 'order' : [[0, 'asc']]});
			 }
		 })

		$("#" + $(this).attr("id") + "_div").show();
	});

	/*
	██████╗ ███████╗██╗     ███████╗ ██████╗  █████╗ ████████╗███████╗███████╗
	██╔══██╗██╔════╝██║     ██╔════╝██╔════╝ ██╔══██╗╚══██╔══╝██╔════╝██╔════╝
	██║  ██║█████╗  ██║     █████╗  ██║  ███╗███████║   ██║   █████╗  ███████╗
	██║  ██║██╔══╝  ██║     ██╔══╝  ██║   ██║██╔══██║   ██║   ██╔══╝  ╚════██║
	██████╔╝███████╗███████╗███████╗╚██████╔╝██║  ██║   ██║   ███████╗███████║
	╚═════╝ ╚══════╝╚══════╝╚══════╝ ╚═════╝ ╚═╝  ╚═╝   ╚═╝   ╚══════╝╚══════╝
-- required since these DOM objects are created after the inital loading*/

	$('body').on('click', '.admin_swaps', function(){
		var template_data = [];
		clearModalTemplate();
		var myName = $(this).data('initiator-name');
		var theirName = $(this).data('receiver-name');
		var swapNumber = $(this).data('swap-pk');
		var stat = $(this).data('swap-status');
		var shift1 = $(this).data('shift1');
		var shift2 = $(this).data('shift2');

			$.ajax({
				url: 'processing.php',
				dataType: 'json',
				data: {getSwapForFinalize: $(this).data('swap-pk')},
				success: function(dat){
					template_data['swaps'] = dat;
					template_data['myName'] = myName;
					template_data['shift1'] = shift1;
					template_data['shift2'] = shift2;
					//console.log("This here " + template_data['shift2']);
					template_data['theirName'] = theirName;
					template_data['swapNumber'] = swapNumber;
					if (stat != 'Declined' && stat != 'Approved' && stat != 'Not Approved' && stat != 'Expired' && stat != 'Sent') {
						$('.modal-body').html(Mustache.render(finalize_swap, template_data));
						$('.modal-title').html("Please confirm.");
					}else if(stat == 'Sent'){
						$('.modal-body').html("You must wait until the receiever approves of this swap.");
						$('.modal-title').html("Please confirm.");
					}else{
						$('.modal-body').html("There are no further actions required for this swap.");
						$('.modal-title').html("Please confirm.");
					}

				}
			});
	});

	$('body').on('change', '.initiator_option', function() {
	    $('.initiator_checkbox').not(this).prop('checked', false);
	});

	//add the selected receiver to the hidden input
	$('body').on("change", '#receiver_select', function() {
		$("#receiver_PK").val($(this).find(":selected").data("receiver_PK"));
	});

	$("body").on("change", "#check_all", function(){
		if ($(this).is(":checked")) {
			$(".check_weekday").prop("checked", true);
		}

	});

	/*Setting up the dynamic option element*/
	$('body').on("change", "#initiator_select", function(){

		if($("#receiver_select")){
			$("#receiver_select").remove();
		};

		$.ajax({
			url: "processing.php",
			dataType: 'json',
			data: {
				'possibleSwaps' : true,
				'date'          : $("#initiator_select").find(":selected").data('date'),
				'Shift_Type'    : $("#initiator_select").find(":selected").data('shift_type'),
				'initiator_shift_id'  : $("#initiator_select").find(":selected").val(),
				'Emp_ID' : $("#initiator_select").find(":selected").data('empid')
				},
			success: function(dat){
				var selectNode = document.createElement('select');
				var att = document.createAttribute("required");
				selectNode.id = "receiver_select";
				selectNode.name = "receiver_select";
				selectNode.className = 'form-control';
				selectNode.setAttributeNode(att);

				var emptyOption = document.createElement("option");
				emptyOption.innerHTML = "--Select a shift--";
				emptyOption.value = "";

				selectNode.appendChild(emptyOption);

				for(var i = 0; i < dat.length; i++){
					var nodie = document.createElement("option");
					nodie.dataset.date = dat[i].Date;
					nodie.dataset.shift_type = dat[i].Shift_Type;
					nodie.dataset.receiver_PK = dat[i].EMP_ID;
					nodie.className = "receiver_option";
					nodie.value = dat[i].Shift_ID;
					nodie.innerHTML = dat[i].EMP_NAME + "--" + dat[i].Date + "--" + dat[i].Shift_Type;
					selectNode.appendChild(nodie);
				}

				document.getElementById('swap_container').appendChild(selectNode);
			},
      error: function(jqXHR, textStatus, errorThrown){
        alert(errorThrown, textStatus);
      }
		});
	});

	//activate acceptance form
	$('body').on('click', '.swap_row', function(){
		clearModalTemplate();
		var template_data = [];
		var stat = $(this).data('swap-status');
		var swapID = $(this).data('swap-pk');
		$.ajax({
			url: 'processing.php',
			dataType: 'json',
			data: {acceptSwap: swapID},
			success: function(dat){
				template_data['swap_info'] = dat;
				console.log(template_data['swap_info']);

				if (stat != 'Sent') {
					$('.modal-body').html(Mustache.render("No further action is required for this swap."));
					$('.modal-title').html("Notice:");
				}else{
					$('.modal-body').html(Mustache.render(accept_swap, template_data));
					$('.modal-title').html("Please confirm the proposed shift swap");
				}
			},
      error: function(jqXHR, textStatus, errorThrown){
        alert(errorThrown, textStatus);
      }
		});
	});

	//activate the status update form
	$("body").on('click', '.past_shifts', function(){

		clearModalTemplate();

		if(isAdmin){
			var shiftid = $(this).data("shiftid");
			var template_data = [];
			$.ajax({
				url: 'processing.php',
				data: {'getUserForUpdate' : shiftid },
				dataType: 'json',
				success: function(dat){
					template_data['shiftid'] = shiftid;
					template_data['Date'] = dat[0].Date;
					template_data['Name'] = dat[0].EMP_NAME;

					if(dat[0].Status != "P"){
						var message = "Shift number " + shiftid + " cannot be updated more than once.";
						$('.modal-body').html(Mustache.render(message, template_data));
					}else{
						$('.modal-body').html(Mustache.render(update_status, template_data));
					}

					$('.modal-title').html("Update Shift Status");
				}
			});
		}else{

		}


	});

	//shift swap template
	$("ul").on('click', '#shift_swap_request_action', function(){
		clearModalTemplate();
		var template_data = [];

		$.ajax({
			url: "processing.php",
			dataType: 'json',
			data: 'startSwap',
			success: function(dat){
				//console.log(dat);
				if(typeof dat[0] === 'undefined' || dat[0] === null){
					$('.modal-body').html(Mustache.render("You are currently not scheduled for a swappable shift", template_data));
					$('.modal-title').html("Notice:");
				}else{
					template_data['possibleShifts'] = dat;
					template_data['foundEmp'] = true;
					template_data['empName'] = dat[0].EMP_NAME;
					template_data['empID'] = dat[0].EMP_ID;
					$('.modal-body').html(Mustache.render(shift_swap_request, template_data));
					$('.modal-title').html("Shift Swap");
				}

			}
		});

	});

	//required on shift stuff
	$("ul").on('click', '#required_on_shift_action', function(){
		clearModalTemplate();
		var template_data = [];
		template_data['title'] = "Required On Shift";
		template_data['year'] = DATEobj.getFullYear();
		template_data['peopleAvailable'] = [];

		$.ajax({
			url: "processing.php",
			dataType: "json",
			data: 'empCount',
			success: function(dat){
				template_data['peopleAvailable'].push(dat.length);
				$('.modal-body').html(Mustache.render(required_on_shift, template_data));
				$('.modal-title').html("How many people would be optimal for next week's shift?");
			}
		});
	});


	$("ul").on('click', '#new_pass_action', function(){
	 clearModalTemplate();
	 var template_data = [];
	 template_data['title'] = "Create a new password";
	 // console.log(template_data);
	 $('.modal-body').html(Mustache.render(new_pass, template_data));
	 	$('.modal-title').html("Create a new password");
 });

	//create employee
	$("ul").on('click', '#create_employee_action', function(){
		clearModalTemplate();
		var template_data = [];
		template_data['title'] = "Create Employee";
		template_data['positions'] = listof_positions;
		// console.log(template_data);
		$('.modal-body').html(Mustache.render(create_employee, template_data));
		$('.modal-title').html("Create Employee");
		$('#super_no').prop("checked", "checked");
	});

	//assign shift rendering
	$("ul").on('click', '#assign_shift_action', function(){
		clearModalTemplate();
		var template_data = [];
		template_data['title'] = "Assign Employee Shift";
		template_data['employees'] = load_contextual_emps;
		template_data['year'] = DATEobj.getFullYear();
		template_data['week'] = DATEobj.getWeek() + 1;
		$('.modal-body').html(Mustache.render(assign_shift, template_data));
		$('.modal-title').html("Assign Employee Shift");
	});

	//creating another position within the company
	$("ul").on('click', '#add_department_action', function(){
		clearModalTemplate();
		var template_data = [];
		template_data['title'] = "Add Department";
		$('.modal-body').html(Mustache.render(add_department, template_data));
		$('.modal-title').html("Add Department");
	});

	//view phone book
	$("ul").on('click', '#phone_book_action', function(){
		clearModalTemplate();
		var template_data = [];
		template_data['title'] = "Phone Book";
		template_data['phonebook'] = phone_number;
		$('.modal-body').html(Mustache.render(phone_book, template_data));
		$('.modal-title').html("Phone Book");
	});


});
