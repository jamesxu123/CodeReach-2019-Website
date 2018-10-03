<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CodeReach Dashboard</title>

    <!-- Bootstrap Core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- SWAL -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.2/sweetalert2.all.min.js"></script>
    
    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    <script>
        function get(name){
           if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
              return decodeURIComponent(name[1]);
        }
        
        if(get('logout') == 'true'){
            swal({
			  title: "Success",
			  text: "You have been logged out!",
			  type: "success",
			});
            sessionStorage.clear();
        }
        else if(get('invalid') == 'true'){
            swal({
			  title: "Error",
			  text: "Your authentication token has expired or is invalid",
			  type: "error",
			});
			sessionStorage.clear();
        }
        else if(get('verifytoken') && get('username')){
            $.ajax({
                type: "POST",
                url: "https://dev.davidhui.ca/codereach/auth.php",
                // The key needs to match your method's input parameter (case-sensitive).
                data: JSON.stringify({ verifytoken: get('verifytoken'), username: get('username')}),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    if(data.code == 400){
                        swal({
            			  title: "Error",
            			  text: "The specified token is invalid!",
            			  type: "error",
            			});
                    }
                    else if(data.code == 200){
                        swal({
            			  title: "Success",
            			  text: "Your account has been verified. You may now login.",
            			  type: "success",
            			});
                    }
                    else if(data.code == 500){
                        swal({
            			  title: "Error",
            			  text: data.message,
            			  type: "error",
            			});
                    }
                },
                failure: function(errMsg) {
                    alert(errMsg);
                }
            });
        }
        else if(get('confirmtoken') && get('appid')){
            $.ajax({
                type: "POST",
                url: "https://dev.davidhui.ca/codereach/auth.php",
                // The key needs to match your method's input parameter (case-sensitive).
                data: JSON.stringify({ confirmtoken: get('confirmtoken'),appid: get('appid')}),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    if(data.code == 400){
                        swal({
            			  title: "Error",
            			  text: "The specified token is invalid!",
            			  type: "error",
            			});
                    }
                    else if(data.code == 200){
                        swal({
            			  title: "Success",
            			  text: "You have confirmed your spot!",
            			  type: "success",
            			});
                    }
                    else if(data.code == 500){
                        swal({
            			  title: "Error",
            			  text: data.message,
            			  type: "error",
            			});
                    }
                },
                failure: function(errMsg) {
                    alert(errMsg);
                }
            });
        }
        
        const usertoken = sessionStorage.getItem("token");
        if(usertoken != null){
            window.location.replace("index.php");
        }
        
    </script>
    <style>
        .swal2-popup {
          font-size: 1.6rem !important;
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">CodeReach - Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" id="loginForm">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Username" name="username" id="username" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" id="password" type="password" value="">
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button class="btn btn-lg btn-success btn-block" id="btnLogin">Login</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>
    
    <script>
        $(document).ready(function() {
            $("#loginForm").submit(function(e){
                return false;
            });
            $("#btnLogin").click(function(){
                let valUsername = document.getElementById("username").value;
                let valPassword = document.getElementById("password").value;
                 $.ajax({
                    type: "POST",
                    url: "https://dev.davidhui.ca/codereach/auth.php",
                    // The key needs to match your method's input parameter (case-sensitive).
                    data: JSON.stringify({ username: valUsername, password: valPassword}),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if(data.code == 401){
                            swal({
                			  title: "Error",
                			  text: "Incorrect username or password!",
                			  type: "error",
                			});
                        }
                        else if(data.code == 200){
                            sessionStorage.setItem("token", data.token);
                            window.location.replace("index.php");
                        }
                    },
                    failure: function(errMsg) {
                        alert(errMsg);
                    }
                });
            }); 
        });
    </script>
</body>

</html>
