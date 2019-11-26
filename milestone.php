<?php
    include 'func.php';
    session_start();
    if (!isset($_SESSION['login_user'])) {
        http_response_code(401);
		echo "<script language=javascript>alert ('login first');</script>"; 
        $_SESSION['userurl'] = $_SERVER['REQUEST_URI']; 
        echo '<script language=javascript>window.location.href="/index.php"</script>'; 
    }
    if(!isset($_GET['deliverable_id'])||!isset($_GET['employee_id'])||!isset($_GET['submission_id'])) {
        http_response_code(400);
		die ("Illegal page. Load in from submission page!<br><a href=\"/index.php\">HOME</a>");
    }
    else if(!$_SESSION['isAdmin']&&!isRelevant($_SESSION['login_user'],$_GET['deliverable_id'])) {
        http_response_code(401); echo "Permission denied.\n";
    }
    $deliverable_id=$_GET['deliverable_id'];
	$employee_id=$_GET['employee_id'];
    $submission_id=$_GET['submission_id'];
    $isEmployee = false;
    if(isRelevant($_SESSION['login_user'],$deliverable_id)) $isEmployee = true;
	
    if($_POST['save']) {
        $submitted_text = $_POST['submitted_text'];
        $sqlConnect = getConn();
        $sqlSave="UPDATE submissions SET status='Saved'
        where deliverableID='$deliverable_id' and employeeID='$employee_id' AND submissionID=$submission_id";
        $update=$sqlConnect->query($sqlSave);
        $sqlSaved="UPDATE submissions SET submitted_text ='".$submitted_text."' WHERE deliverableID='".$deliverable_id."' and employeeID='".$employee_id."' AND submissionID=$submission_id";
        $insertinto=$sqlConnect -> query($sqlSaved);
    }
    
    if($_POST['seen']) {
        $sqlConnect = getConn();
        $sqlSeen="UPDATE submissions SET status='Seen'
		where deliverableID='$deliverable_id' and employeeID='$employee_id' AND submissionID=$submission_id";
        $update=$sqlConnect->query($sqlSeen);
    }
	
	
    if($_POST['accepted']) {
        $sqlConnect = getConn();
        $sqlAccept="UPDATE submissions SET status='Accepted'
		where deliverableID='$deliverable_id' and employeeID='$employee_id' AND submissionID=$submission_id";
	    $update=$sqlConnect->query($sqlAccept);
    }
	
    if($_POST['returned']) {
        $sqlConnect = getConn();
        $sqlReturn="UPDATE submissions SET status='Returned'
		where deliverableID='$deliverable_id' and employeeID='$employee_id' AND submissionID=$submission_id";
        $update=$sqlConnect->query($sqlReturn);
    }
	
    if($_POST['submit']) {
        $sqlConnect = getConn();
        $sqlSubmit="UPDATE submissions SET status='Submitted'
		where deliverableID='$deliverable_id' and employeeID='$employee_id' AND submissionID=$submission_id";
	    $update=$sqlConnect->query($sqlSubmit);
        if ($update=== TRUE) {
			echo "Submitted";
        } 
        else {
            echo "Error updating record: " . $sqlConnect->error;
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    
  <style>
  
  u.unpressed {
  large dark color="#6495ED";
}
  
    table {
      font-family: arial, sans-serif;
      border-collapse: collapse;
      width:70%; 
      margin-left:auto; 
      margin-right:auto;
      margin-top:5%; 
      margin-bottom:auto;  
    }
    h2, h5 {
      text-align: center;
    }
    tr, td, th {
      border: 1px solid #dddddd;
      text-align: center;
      padding: 8px  ;
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
<body>
  <div id="app">
    <v-app>
      <v-card flat light height = "64px" tile mb-5>
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
<h1 style="color:#6495ED">
 Hi, <?php echo $_SESSION['firstname']; ?>
</h1>
<div></div>
<div></div>
<div></div>

<h2 style="color:#6495ED">
  Milestone Submission
</h2>
<h5 style="color:#6495ED">
  <?php getDeliverableTitle($deliverable_id,$employee_id);?><br>
    <?php
    if($_SESSION['isAdmin']) {
    echo "<a href='/TrackingProgress.php' class='button2' aria-label='Left Align' name='trackingProgress' value='tracking_progress'>Tracking Progress</a></button>";
    echo "<font size=\"2\">/  </font>";
    echo "<a href='/workevidence_new.php?deliverable_id=".$_GET['deliverable_id']."&employee_id=".$_GET['employee_id']."' class='button2' aria-label='Left Align' name='workevidence' value='tracking_progress'>Work Evidence</a></button>";
    }
    else {
        echo "<a href='/EmployeeView.php' class='button2' aria-label='Left Align' name='workevidence' value='tracking_progress'>Employee View</a></button>";
    }
    echo "<font size=\"2\">/  </font><a href='/submission.php?deliverable_id=".$_GET['deliverable_id']."&employee_id=".$_GET['employee_id']."' class='button2' aria-label='Left Align' name='submission' value='submission'>Submission</a></button>";
    ?>
</h5>

 <div style="margin-top: auto;vertical-align: middle;">
 </div>
<v-row>
  <v-col cols="12" md="6">
    <v-textarea
    outlined    
    name="submitted_text"
    label="Submitted text"
    form="milestoneAction"
    value="<?php getTextarea($deliverable_id,$employee_id,$submission_id);?>"
    ></v-textarea>
    </v-col>
    
    <v-col cols="12" md="6">
      <table>
        <tr>
          <th colspan="2">File attachment</th>
        </tr>
          <?php getFileTable($deliverable_id,$employee_id,$submission_id);?>
      </table>
    <div class="text-right mt-3" <?php if($_SESSION['isAdmin']) echo "hide";?>>
      <form id="uploadForm" action="/upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file[]" accept=".pdf,.jpg,.jpeg,.png">
        <input type="text" class="hide" name="submission_id" value="<?php echo $submission_id; ?>">
        <input type="text" class="hide" name="deliverable_id" value="<?php echo $deliverable_id; ?>">
        <input type="text" class="hide" name="employee_id" value="<?php echo $employee_id; ?>">
        <v-btn small dark color='#6495ED' style='margin-left: 20px'>
            <input type="submit" value="Upload">
        </v-btn>
        </form>
    </div>
    <script>
        var myElement = document.getElementById("file");
        if(myElement!=null)
        document.getElementById("uploadForm").innerHTML = "";
    </script>
    </v-col>
    </v-row>
     

  
 
          
<form action="<?php echo "/milestone.php?deliverable_id=$deliverable_id&employee_id=$employee_id&submission_id=$submission_id"; ?>" id="milestoneAction" name="milestoneAction" method="POST">
  <div class="text-center <?php if(!$_SESSION['isAdmin']) echo "hide";?>">
  
   <?php if(!$_POST['seen']){ 
   echo"<v-btn large dark color='#6495ED'><input type='submit' name='seen' value='Mark As Seen'/></v-btn>";} ?>
   <?php if($_POST['seen']){ 
   echo"<v-btn large dark color='#00b300'><input type='submit' name='seen' value='Marked As Seen'/></v-btn>";} ?>
   
  </div>
  <div class="text-center <?php if(!$_SESSION['isAdmin']) echo "hide";?>" style="margin-top: 40px">
  
  <?php if(!$_POST['accepted']){
  echo"<v-btn large dark color='#6495ED'><input type='submit' name='accepted' value='Accept'/></v-btn>";} ?>
   <?php if($_POST['accepted']){
  echo"<v-btn large dark color='#00b300'><input type='submit' name='accepted' value='Accepted'/></v-btn>";} ?>
  
  <?php if(!$_POST['returned']){
  echo"<v-btn large dark color='#6495ED' style='margin-left: 20px'><input type='submit' name='returned' value='Return For Revision'/></v-btn>";} ?>
   <?php if($_POST['returned']){
  echo"<v-btn large dark color='#00b300' style='margin-left: 20px'><input type='submit' name='returned' value='Returned For Revision'/></v-btn>";} ?>
  
   </div>
   <div class="text-right <?php if(!$isEmployee) 	 "hide";?>" style="margin-top: 40px">
   
   <?php if(!$_POST['save']&&($_SESSION['isAdmin']||$employee_id==$_SESSION['login_user'])){
  echo" <v-btn large dark color='#6495ED'><input type='submit' name='save' value='Save'/></v-btn>";} ?>
     <?php if($_POST['save']){
  echo" <v-btn large dark color='#00b300'><input type='submit' name='save' value='Saved'/></v-btn>";} ?>
  
  <?php if(!$_POST['submit']&&($_SESSION['isAdmin']||$employee_id==$_SESSION['login_user'])){
   echo"<v-btn large dark color='#6495ED' style='margin-left: 20px'><input type='submit' name='submit' value='Submit'/></v-btn>";} ?>
  <?php if($_POST['submit']){
   echo"<v-btn large dark color='#00b300' style='margin-left: 20px'><input type='submit' name='submit' value='Submitted'/></v-btn>";} ?>
   </div>
          </form>
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
</body>
</html>