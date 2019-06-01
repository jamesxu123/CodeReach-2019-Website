// form_ajax.js
$(document).ready(function() {

    // process the form
    $('form').submit(function(event) {
        console.log("START");
        // get the form data
        // there are many ways to get this data using jQuery (you can use the class or id also)
        var formData = {
            'name'              : $('input[name=demo-name]').val(),
            'email'             : $('input[name=demo-email]').val(),
            'message'           : $('textarea[name=demo-message]').val(),
        };
	console.log(formData);
        console.log("RETRIEVED FORM DATA");
        // process the form
        $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : '/formhandler.php', // the url where we want to POST
            data        : formData, // our data object
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true	
	})
            // using the done promise callback
            .done(function(data) {

                // log data to the console so we can see
		console.log("PLZ WORK");
                console.log(data); 
		console.log("HELLO WORLD");
                if (!data.success) {
                    swal({
                        title: "Sorry!",
                        text: "Please try again!",
                        icon: "error",
                    });
                }
                else {
                    swal({
                        title: "Success!",
                        text: "You'll be hearing from CodeReach soon!",
                        icon: "success",
                    });
                }
            });

        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
    });

});
