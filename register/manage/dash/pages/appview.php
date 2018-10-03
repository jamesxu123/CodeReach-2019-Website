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

    <!-- Morris Charts CSS -->
    <link href="../vendor/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    
    <!-- SWAL -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.2/sweetalert2.all.min.js"></script>
    
    <!-- JWT -->
    <script src="../js/jwt-decode.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>
        function get(name){
           if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
              return decodeURIComponent(name[1]);
        }
        const usertoken = sessionStorage.getItem("token");
        
        let isAdmin = false;
        if(jwt_decode(usertoken).role < 2){
            isAdmin = true;
        }
        
        if(usertoken == null){
            window.location.replace("login.php");
        }
    </script>
    <style>
        .fieldlabel{
            font-size:15pt;
            padding-right:0.7em;
            padding-bottom:0.5em;
        }
        .swal2-popup {
          font-size: 1.6rem !important;
        }
    </style>
    
</head>

<body>

    <div id="wrapper">

        <?php require('./sidebar.php')?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">View Application - <span id="appstatus"></span></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <span id="application"></span>
                    <br>
                    <span id="adminButtons" hidden>
                        <button type="button" id="appaccept" class="btn btn-success">Accept</button>
                        <button type="button" id="appwait" class="btn btn-warning">Waitlist</button>
                        <button type="button" id="appreject" class="btn btn-danger">Reject</button>                        
                    </span>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../data/morris-data.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>
    <script>
        var userInfo = jwt_decode(usertoken);
        
        if(userInfo["role"] < 1){
            $('#adminButtons').show();
        }
        
        var reqID = get('id');
        var appData = '';
        $.ajax({
            type: "POST",
            url: "https://dev.davidhui.ca/codereach/api.php",
            // The key needs to match your method's input parameter (case-sensitive).
            data: JSON.stringify({ token: usertoken, action: "getapplication", appid:reqID}),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data){
                if(data.code == 200){
                    appData = data;
                }
                else if(data.code == 403){
                    sessionStorage.clear();
                    window.location.replace("login.php?invalid=true");
                }
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
        
        function populateInfo(data){
            appData = data;
            console.log(data);
            
            eltAppStatus = document.getElementById("appstatus")
            if(data.application.status == 0){
                eltAppStatus.innerHTML = "Unreviewed";
            }
            else if(data.application.status == 1){
                eltAppStatus.setAttribute("style", "color:red;");
                eltAppStatus.innerHTML = "Denied";
                $('#appreject').prop('disabled', true);
            }
            else if(data.application.status == 2){
                eltAppStatus.setAttribute("style", "color:green;");
                eltAppStatus.innerHTML = "Accepted";
                $('#appaccept').prop('disabled', true);
            }
            else if(data.application.status == 3){
                eltAppStatus.setAttribute("style", "color:gold;");
                eltAppStatus.innerHTML = "Waitlisted";
                $('#appwait').prop('disabled', true);
            }
            else if(data.application.status == 4){
                eltAppStatus.setAttribute("style", "color:green;");
                eltAppStatus.innerHTML = "Confirmed";
                $('#appaccept').prop('disabled', true);
                $('#appwait').prop('disabled', true);
                $('#appreject').prop('disabled', true);
            }
            else{
                eltAppStatus.setAttribute("style", "color:grey;");
                eltAppStatus.innerHTML = "Manual Status";
                $('#appaccept').prop('disabled', true);
                $('#appwait').prop('disabled', true);
                $('#appreject').prop('disabled', true);
            }
            
            
            let domElement = '<input id="appid" type="hidden" value="' + data.application.id + '"><br>';
            domElement+= '<span class="fieldlabel">Fullname:</span> <span id="appfullname">'+data.application.fullname+'</span><br>';
            domElement+= '<span class="fieldlabel">Birthday:</span> ' + data.application.birthday+'<br>';
            domElement+= '<span class="fieldlabel">Grade:</span> ' + data.application.grade+'<br>';
            domElement+= '<span class="fieldlabel">Email:</span> ' + data.application.email+'<br>';
            domElement+= '<span class="fieldlabel">Gender:</span> ' + data.application.gender+'<br>';
            domElement+= '<span class="fieldlabel">Shirt Size:</span> ' + data.application.shirtsize+'<br>';
            domElement+= '<span class="fieldlabel">Experience:</span> ' + data.application.experience+'<br>';
            domElement+= '<span class="fieldlabel">Current School:</span> ' + data.application.currentschool+'<br>';
            domElement+= '<span class="fieldlabel">Parent\'s Name:</span> ' + data.application.parent_fullname+'<br>';
            domElement+= '<span class="fieldlabel">Parent\'s Email:</span> ' + data.application.parent_email+'<br>';
            domElement+= '<span class="fieldlabel">Parent\'s Phone Number:</span> ' + data.application.parent_phone+'<br>';
            domElement+= '<span class="fieldlabel">Emergency Number 1:</span> ' + data.application.emergencyone+'<br>';
            domElement+= '<span class="fieldlabel">Emergency Number 2:</span> ' + data.application.emergencytwo+'<br>';
            domElement+= '<span class="fieldlabel">Can walk home:</span> ' + data.application.canwalk+'<br>';
            
            if(!isAdmin){
                domElement+= '<br><span class="fieldlabel">Missing or incorrect information? Please email us at <a href="mailto:hello@codereach.ca">hello@codereach.ca</a> with your application ID (' + data.application.id+') and we will be glad to assist you.<br>';
            }
            document.getElementById("application").innerHTML = domElement;
        }
        
        $(document).ready(function() {
            
            if(isAdmin){
                document.getElementById("applicationstab").setAttribute("style", "");
            }
            else{
                document.getElementById("applicationtab").setAttribute("style", "");
                document.getElementById("userlinkself").href = "appview.php?id="+userInfo.application;
                $('#userlinkself').toggleClass('active');
            }
            
            if(appData){
                populateInfo(appData);
            }else{
                $.ajax({
                    type: "POST",
                    url: "https://dev.davidhui.ca/codereach/api.php",
                    // The key needs to match your method's input parameter (case-sensitive).
                    data: JSON.stringify({ token: usertoken, action: "getapplication", appid:reqID}),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if(data.code == 200){
                            populateInfo(data);
                        }
                        else if(data.code == 403){
                            sessionStorage.clear();
                            window.location.replace("login.php?invalid=true");
                        }
                    },
                    failure: function(errMsg) {
                        alert(errMsg);
                    }
                });
            }
             
            
            $("#appaccept").click(function(){
                
                swal({
                  title: 'Are you sure?',
                  html: 'You are about to <span style="color:green;font-weight:bold;">ACCEPT</span> '+document.getElementById("appfullname").innerHTML,
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes!'
                }).then((result) => {
                  if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "https://dev.davidhui.ca/codereach/api.php",
                        // The key needs to match your method's input parameter (case-sensitive).
                        data: JSON.stringify({ token: usertoken, action: "acceptapplication", appid:reqID}),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(data){
                            let textstr = "You have accepted "+appData.application.fullname;
                            console.log(textstr);
                            console.log(data);
                            if(data.code == 200){
                                swal({
                    			  title: "Success",
                    			  text: textstr,
                    			  type: "success"
                    			});
                    			eltAppStatus.setAttribute("style", "color:green;");
                                eltAppStatus.innerHTML = "Accepted";
    
                            }else if(data.code == 403){
                                swal({
                    			  title: "Hey!",
                    			  text: "You're not allowed to do that!",
                    			  type: "error"
                    			});
                            }
                        },
                        failure: function(errMsg) {
                            alert(errMsg);
                        }
                    });
                  }
                                
                
                });     
            }); 
            
            $("#appwait").click(function(){
                swal({
                  title: 'Are you sure?',
                  html: 'You are about to <span style="color:#ffd700;font-weight:bold;">WAITLIST</span> '+document.getElementById("appfullname").innerHTML,
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes!'
                }).then((result) => {
                  if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "https://dev.davidhui.ca/codereach/api.php",
                        // The key needs to match your method's input parameter (case-sensitive).
                        data: JSON.stringify({ token: usertoken, action: "waitlistapplication", appid:reqID}),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(data){
                            let textstr = "You have waitlisted "+appData.application.fullname;
                            console.log(textstr);
                            console.log(data);
                            if(data.code == 200){
                                swal({
                    			  title: "Success",
                    			  text: textstr,
                    			  type: "success"
                    			});
                    			eltAppStatus.setAttribute("style", "color:gold;");
                                eltAppStatus.innerHTML = "Waitlisted";
                            }else if(data.code == 403){
                                swal({
                    			  title: "Hey!",
                    			  text: "You're not allowed to do that!",
                    			  type: "error"
                    			});
                            }
                        },
                        failure: function(errMsg) {
                            alert(errMsg);
                        }
                    });
                  }
                                
                
                });     
            }); 
            
            $("#appreject").click(function(){
                swal({
                  title: 'Are you sure?',
                  html: 'You are about to <span style="color:red;font-weight:bold;">REJECT</span> '+document.getElementById("appfullname").innerHTML,
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes!'
                }).then((result) => {
                  if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "https://dev.davidhui.ca/codereach/api.php",
                        // The key needs to match your method's input parameter (case-sensitive).
                        data: JSON.stringify({ token: usertoken, action: "denyapplication", appid:reqID}),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(data){
                            let textstr = "You have rejected "+appData.application.fullname;
                            console.log(textstr);
                            console.log(data);
                            if(data.code == 200){
                                swal({
                    			  title: "Success",
                    			  text: textstr,
                    			  type: "success"
                    			});
                    			eltAppStatus.setAttribute("style", "color:red;");
                                eltAppStatus.innerHTML = "Rejected";
    
                            }else if(data.code == 403){
                                swal({
                    			  title: "Hey!",
                    			  text: "You're not allowed to do that!",
                    			  type: "error"
                    			});
                            }
                        },
                        failure: function(errMsg) {
                            alert(errMsg);
                        }
                    });
                  }
                                
                
                });     
            }); 
        });
            

        </script>

</body>

</html>
