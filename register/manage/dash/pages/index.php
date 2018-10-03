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
    
    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    
    <!-- JWT -->
    <script src="../js/jwt-decode.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    <script>
        const usertoken = sessionStorage.getItem("token");
        if(usertoken == null){
            window.location.replace("login.php");
        }
        
        let isAdmin = false;
        let user_decoded = jwt_decode(usertoken);
        if(user_decoded.role < 2){
            isAdmin = true;
        }
        
        var statsData;
        
        if(isAdmin){
            $.ajax({
            type: "POST",
            url: "https://dev.davidhui.ca/codereach/api.php",
            // The key needs to match your method's input parameter (case-sensitive).
            data: JSON.stringify({ token: usertoken, action: "getstats"}),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data){
                statsData = data;
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
        }
        
        
    </script>
    <div id="wrapper">

       <?php require('./sidebar.php')?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header" id="page-header">At a Glance</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row" id="userwelcome" hidden>
                <div class="col-lg-12">
                    <p id="userwelcometext"></p>
                </div>
                
            </div>
            
            <div class="row" id="adminstats" hidden>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-comments fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge" id="stattotal"></div>
                                    <div>Applications</div>
                                </div>
                            </div>
                        </div>
                        <a href="review.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tasks fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge" id="stataccepted"></div>
                                    <div>Accepted</div>
                                </div>
                            </div>
                        </div>
                        <a href="review.php?query=accepted">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-shopping-cart fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge" id="statwaitlisted"></div>
                                    <div>Waitlisted</div>
                                </div>
                            </div>
                        </div>
                        <a href="review.php?query=waitlisted">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-support fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge" id="statrejected"></div>
                                    <div>Rejected</div>
                                </div>
                            </div>
                        </div>
                        <a href="review.php?query=rejected">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
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
        function populateStats(data){
            document.getElementById("stattotal").innerHTML = data.stats.total;
            document.getElementById("stataccepted").innerHTML = data.stats.accepted;
            document.getElementById("statwaitlisted").innerHTML = data.stats.waitlisted;
            document.getElementById("statrejected").innerHTML = data.stats.rejected;
        }
        
        $( document ).ready(function() {
            
            //show or hide elements
            
            if(isAdmin){
                $('#adminstats').show();
                document.getElementById("applicationstab").setAttribute("style", "");
            }
            else{
                $('#userwelcome').show();
                document.getElementById("applicationtab").setAttribute("style", "");
                document.getElementById("userlinkself").href = "appview.php?id="+user_decoded.application;
                document.getElementById("page-header").innerHTML = "Hey, there!";
                document.getElementById("userwelcometext").innerHTML = "You can check your application status in the 'Application' tab.";
            }
            
            if(isAdmin){
                if(statsData){
                    populateStats(statsData);
                }
                else{
                    $.ajax({
                    type: "POST",
                    url: "https://dev.davidhui.ca/codereach/api.php",
                    // The key needs to match your method's input parameter (case-sensitive).
                    data: JSON.stringify({ token: usertoken, action: "getstats"}),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if(data.code == 200){
                            console.log(data);
                            populateStats(data);
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
            }
            
        });
    </script>
</body>

</html>
