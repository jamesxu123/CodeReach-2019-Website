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
        
        function get(name){
           if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
              return decodeURIComponent(name[1]);
        }
        
        function myFunction(query=null) {
          console.log('hi');
          // Declare variables 
          var input, filter, table, tr, td, i;
          
          if(query){
            filter = query.toUpperCase();
          }else{
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
          }
          
          table = document.getElementById("applist");
          tr = table.getElementsByTagName("tr");
        
          // Loop through all table rows, and hide those who don't match the search query
          for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) {
              if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
              } else {
                tr[i].style.display = "none";
              }
            } 
          }
        }
    </script>
    <div id="wrapper">

        <?php require('./sidebar.php'); ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Applications</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Enter a status..." style="width:75vw">
                    <br>
                    <table class="table" style="width:75vw" id="applist">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Name</th>
                          <th scope="col">Grade</th>
                          <th scope="col">School</th>
                          <th scope="col">Status</th>
                          <th scope="col">View</th>
                        </tr>
                      </thead>
                      <tbody id="applicationtable">
                        
                      </tbody>
                    </table>
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
             $.ajax({
                type: "POST",
                url: "https://dev.davidhui.ca/codereach/api.php",
                // The key needs to match your method's input parameter (case-sensitive).
                data: JSON.stringify({ token: usertoken, action: "allapplications"}),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    console.log(data.applications);
                    
                    for(application of data.applications){
                        var appstate;
                        if(application.status == 0){
                            appstate = 'Unreviewed';
                        }
                        else if(application.status == 1){
                            appstate = 'Rejected';
                        }
                        else if(application.status == 2){
                            appstate = 'Accepted';
                        }
                        else if(application.status == 3){
                            appstate = 'Waitlisted';
                        }
                        else if(application.status == 4){
                            appstate = 'Confirmed';
                        }
                        else{
                            appstate = 'Manual';
                        }
                        let domString = '<tr><th scope="row">' + application.id +'</th><td>' + application.fullname + '</td><td>' + application.grade + '</td><td>' + application.currentschool + '</td><td>'+ appstate +  '</td><td><a href="appview.php?id='+application.id+'">View</a></td></tr>';
                        $("#applicationtable").append(domString);
                    }
                    myFunction(get('query'));
                },
                failure: function(errMsg) {
                    alert(errMsg);
                }
            });

        </script>

</body>

</html>
