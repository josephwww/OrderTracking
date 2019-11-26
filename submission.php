<?php
    include 'func.php';
    session_start();
    //doesnot login
    if (!isset($_SESSION['login_user'])) {
        http_response_code(401);
		echo "<script language=javascript>alert ('login first');</script>"; 
        $_SESSION['userurl'] = $_SERVER['REQUEST_URI']; 
        echo '<script language=javascript>window.location.href="/index.php"</script>'; 
    }
    //illegal page
    if(!isset($_GET['deliverable_id'])||!isset($_GET['employee_id'])) {
        http_response_code(400);
		die ("Illegal page. Load in from employee view!<br><a href=\"index.php\">HOME</a>");
    }
    //
    else if(!$_SESSION['isAdmin']&&!isRelevant($_SESSION['login_user'],$_GET['deliverable_id'])) {
        http_response_code(401); die("Permission denied.\n");
    }
    $deliverable_id=$_GET['deliverable_id'];
	$employee_id=$_GET['employee_id'];

    $isEmployee = false;
    if(isRelevant($_SESSION['login_user'],$deliverable_id)) $isEmployee = true;
?>

<!DOCTYPE html>
<html>
<head>
    
<style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width:70%; 
          margin-left:auto; 
          margin-right:auto;
          margin-top:5%; 
          margin-bottom:auto;  
        }


        h2 {
          text-align: center;
        }
      

        tr, td, th {
          border: 1px solid #dddddd;
          text-align: center;
          padding: 8px;
        }
        
        th{
          color: cornflowerblue;
        }

        .hide {
            display:none;
        }
        
        .button2 {
          border: none;
          color: #6495ED;
          padding: 6px 7px;
          text-align: center;
          text-decoration: none;
          display: inline-block;
          font-size: 12px;
          margin: 1px 1px;
          cursor: pointer;
        }

        </style>
           <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@3.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    
    </head>
<body onload="showSubmissionTable()">
  <div id="app">
    <v-app>
            <v-card
            flat
            light
            height = "64px"
            tile
            mb-5
            >
            <v-toolbar dark color = "#6495ED">
        
              <v-toolbar-title class = "title" > <a href = "/" style = "text-decoration: None" class = "text-white">Sustech</a></v-toolbar-title>
         
              <v-spacer></v-spacer>
              <v-btn style = "text-decoration: None" href = "#" text>
                    <span mr-5>Admin</span>
              </v-btn>
              <v-btn style = "text-decoration: None" href = "#" text>
                    <span mr-5>Home</span>
              </v-btn>
              <v-btn style = "text-decoration: None" href = "/logout.php" text>
                    <span>Logout</span>
              </v-btn>

            </v-toolbar>
          </v-card>

<v-container>
      <v-content>
<h1 style="color: cornflowerblue">
 Hi, <?php echo $_SESSION['firstname'];?>
</h1>
<div style="color: cornflowerblue" ml-5> Total Project Time Spent: <?php $time=getTotalTime($deliverable_id);echo $time;?></div>
<div></div>
<div></div>
<br>
<div style="
    width:80%; 
    margin-left:auto; 
    margin-right:auto;
    margin-top:5%; 
    margin-bottom:auto;
">
    <v-progress-linear
      color="light-blue"
      height="30"
      value="<?php getProgress($deliverable_id);?>"
      striped
    ><?php getProgress($deliverable_id);?>%
    </v-progress-linear>
</div>
  <br>
  <br>
<h2 style="color: cornflowerblue">
  <?php getDeliverableName($deliverable_id); ?><br>
    
    <?php
    if($_SESSION['isAdmin']) {
    echo "<a href='/TrackingProgress.php' class='button2' aria-label='Left Align' name='trackingProgress' value='tracking_progress'>Tracking Progress</a></button>";
    echo "<font size=\"2\">/    </font>";
    echo "<a href='/workevidence.php?deliverable_id=".$_GET['deliverable_id']."&employee_id=".$_GET['employee_id']."' class='button2' aria-label='Left Align' name='workevidence' value='tracking_progress'>Work Evidence</a></button>";
    }
    else {
        echo "<a href='/EmployeeView.php' class='button2' aria-label='Left Align' name='workevidence' value='tracking_progress'>Employee View</a></button>";
    }
    ?>
</h2>

<table class="center" style="width:80%">
    <thead>
        <tr>
          <th>Work Contractor</th>
          <th>File</th>
          <th>Submitted Text</th> 
          <th>Action</th>
        </tr>   
    </thead>
    
    <tbody>
    <?php getSubmissionTable($deliverable_id,$employee_id,0);?>
    </tbody>  
</table>
  <br>
<div mt-2 style="margin-left:10%" class="<?php if(!isRelevant($_SESSION['login_user'],$_GET['deliverable_id'])) echo "hide"; ?>">
    <form method="POST" action="/newSubmission.php?deliverable_id=<?php echo $deliverable_id;?>&employee_id=<?php echo $employee_id;?>">
    <input class="hide" name="new_submission" value="true">
    <v-btn small dark color="#6495ED" type="submit">New Submission</v-btn>
    </form>
</div>

  <h2 style="color: cornflowerblue">
      <small><small>
    History 
    </small></small>
      </h2>
<table class="center" style="width:80%" >
  <thead>
    <tr>
    <th>Work Contractor</th>
    <th>Time Submitted</th>
    <th>File</th>
    <th>Submitted Text</th> 
    <th>Action</th>
  </tr>   
  </thead>
  <tbody>
    <?php getSubmissionTable($deliverable_id,$employee_id,1);?>
    </tbody>    
       

</table>


</v-content>
    </v-container>
    </v-app>
</div>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script>
    new Vue({
      el: '#app',
      vuetify: new Vuetify(),
    })
    
  </script>

<script>
    export default {
      data: () => ({
        date: new Date().toISOString().substr(0, 7),
        menu: false,
        menu1: false,
      }),
    }
  </script>
<script>
        import Vue from 'vue';
Vue.forceUpdate();

// Using the component instance
export default {
  methods: {
    methodThatForcesUpdate() {
      // ...
      this.$forceUpdate();  // Notice we have to use a $ here
      // ...
    }
  }
}
    </script>

    
</body>
</html>

