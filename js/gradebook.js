jQuery(function($) {
    // make tables sortable ('cause sortable is cool!')
    //$("table").dataTable({bPaginate: false, bSearchable: false});
    $("table").dataTable();

	// change each permission to the next value
	$("#permissionTable button").click(function() {
		var current = $(this).text();
		var next = undefined;
		if (current == 'read')
			next = 'write';
		else if (current == 'write')
			next = 'read';
		$(this).text(next);
	});

	// collapsable lab/student lists
	$('li.lab li.student').hide();
	$('li.lab').click(function() {
		$(this).find('li.student').toggle();
	});

	// hide the loading indicator
	$('img.loading').hide();
});

// send any updates back to the server
function submitUpdate(args) {
	$('img.loading').show();
	args['submit'] = true;
	$.ajax({
		url: '#',
		type: 'POST',
		data: args,
		dataType: 'json',
		success: function(data) {
			$('img.loading').hide();
			if (data['success']) {
				$('#feedback').html('Saved change. Page will refresh in <span id="timer">5</span> seconds.');
                            var countdown = function() {
                                var time = parseInt($('#timer').text());
                                if (time == 0)
                                  document.location.reload()
                                else {
                                  $('#timer').text(time - 1);
                                    setTimeout(countdown, 1000);
                                }
                            };
                            setTimeout(countdown, 1000);
			} else {
				$('#feedback').text('Failed to save: ' + data['reason']);
			}
		},
		error: function(req, err) {
			$('img.loading').hide();
			$('#feedback').text('Failed to save: ' + err);
		}
	});
}

// special function to collect rollboxes
function submitRollback() {
	if (confirm("Are you sure you wish to rollback these grades?\n\nThis action cannot be undone.")) {

		var rollbacks = [];
		$(':checkbox').each(function () {
			if ($(this).attr('checked')) {
				rollbacks.push($(this).attr('name'));
			}
		});
		submitUpdate({rollbacks: rollbacks.join(';')});

	}
}

// function to save Staff Permissions
function savePermissions(args) {
        var Permissions = [];
        $('td button').each(function () {
                Permissions.push($(this).attr('name') + '=' + $(this).text());
        });
        submitUpdate({Staff:args,Permissions: Permissions.join(';')});
        }
