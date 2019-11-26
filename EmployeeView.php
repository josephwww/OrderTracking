
<!DOCTYPE html>
<html>
<head>


    <script>
function filter(str1, str2){
    var x = document.querySelectorAll("td.SubjobName");
    var z = document.querySelectorAll("tr.ProjectName");
    var y=document.querySelectorAll("tr");
    for(j in y) {
        y[j].style="display:all";
    }
    for (i in z){
      if (z[i].getAttribute("name") != str2){
        z[i].style = "display:none";
        child = z[i].getElementsByClassName("SubjobName")[0];
    } 
	  else if (child.getAttribute("name") != str1) {
        child.parentNode.style = "display:none";
    }
}
}


    </script>
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
    
    th {
      color: cornflowerblue;
    }

    .hide {
      display:none;
    }

  </style>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/vuetify/1.5.16/vuetify.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/vuetify/1.5.16/vuetify.css.map" rel="stylesheet">
  <link href="  https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    
 </head>
<body>

<?php 
	header('Content-type:text/html; charset=utf-8');
	// open Session
  session_start();

    if (!isset($_SESSION['login_user'])) {
        echo "<script language=javascript>alert ('Please Login');</script>"; 
        $_SESSION['userurl'] = $_SERVER['REQUEST_URI']; 
        echo '<script language=javascript>window.location.href="/index.php"</script>'; 
    }
	
  ?>

  <div id="app">
    <v-app>
          
            <v-toolbar dark color = "#6495ED">

              <v-toolbar-title class = "title" > <a href = "/" style = "text-decoration: None" class = "text-white">Sustech</a></v-toolbar-title>

              <v-spacer></v-spacer>
              <v-btn flat style = "text-decoration: None" href = "#" text>
                    <span mr-5>Admin</span>
              </v-btn>
              <v-btn flat style = "text-decoration: None" href = "#" text>
                    <span mr-5>Home</span>
              </v-btn>
              <v-btn flat style = "text-decoration: None" href = "/logout.php" text>
                    <span>Logout</span>
              </v-btn>

            </v-toolbar>
            <v-container>
  <v-navigation-drawer clipped
    v-model="drawer"
    absolute
    temporary
  >
  <v-list-item>
  

  <v-divider></v-divider>

  <v-list dense>

    <v-list-item
     
      link
    >

      <v-list-item-content>



 <?php
include "func.php";
if (session_id() == ''){
 session_start();
}
  // Connect to DB
  $username = $_SESSION['login_user'];
  if(!$username) {
    http_response_code(401); die("bad username");
  }

  $conn = getConn();
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Generate SQL Query
  $sql = "SELECT
            DISTINCT jobs.JobNumber AS JobID,
            jobs.Name AS ProjectName            
          FROM jobs
          left outer JOIN subjobs
            ON subjobs.jobID = jobs.JobNumber
          left outer JOIN tasks ON (
            tasks.Job = jobs.JobNumber
            AND tasks.Subjob = subjobs.Number
          )
          left outer JOIN tasksAssigned ON (
            tasksAssigned.jobID = jobs.JobNumber
            AND tasksAssigned.subjobID = subjobs.Number
            And tasksAssigned.taskID = tasks.Number
          )
          left outer JOIN deliverables AS deliv ON (
            deliv.jobID = jobs.JobNumber
            AND deliv.subjobID = subjobs.Number
            And deliv.taskID = tasks.Number
          )
          left outer JOIN deliverableAssigned as delivAssigned ON (
            delivAssigned.deliverableID = deliv.deliverableID
            AND delivAssigned.employeeID = tasksAssigned.employeeID
          )
          left outer JOIN times ON (
            times.jobID = jobs.JobNumber
            AND times.subjob = subjobs.Number
            And times.task = tasks.Number
            And times.employeeID = tasksAssigned.employeeID
          )
          WHERE delivAssigned.employeeID = '" . $_SESSION['login_user'] . "' "
  ;
            
  // Query Database
  $result = $conn->query($sql);

  // Print Results to Webpage
  if ($result->num_rows > 0){ // If has deliverables/milestones, print them
    $fields = array("ProjectName", "JobID");

    // Display each fetched row
    while ($row = $result->fetch_array()){
      echo "
      <v-list-tile>
      <v-menu offset-y >
      <v-btn class =\"pr-5\"  flat slot=\"activator\">
      <v-icon left style = \"color: #6495ED\">expand_more</v-icon>
      <span>";
      $field = $fields[0];
      echo $row[$field];
      echo "</span>
      </v-btn>
      ";
      $field = $fields[1];
      $req = $row[$field];
                    $newsql = "SELECT
                    DISTINCT subjobs.Name AS SubjobName,
                    subjobs.Number AS SubjobNumber,
                    jobs.JobNumber AS JobID
                  FROM jobs
                  left outer JOIN subjobs
                    ON subjobs.jobID = jobs.JobNumber
                  left outer JOIN tasks ON (
                    tasks.Job = jobs.JobNumber
                    AND tasks.Subjob = subjobs.Number
                  )
                  left outer JOIN tasksAssigned ON (
                    tasksAssigned.jobID = jobs.JobNumber
                    AND tasksAssigned.subjobID = subjobs.Number
                    And tasksAssigned.taskID = tasks.Number
                  )
                  left outer JOIN deliverables AS deliv ON (
                    deliv.jobID = jobs.JobNumber
                    AND deliv.subjobID = subjobs.Number
                    And deliv.taskID = tasks.Number
                  )
                  left outer JOIN deliverableAssigned as delivAssigned ON (
                    delivAssigned.deliverableID = deliv.deliverableID
                    AND delivAssigned.employeeID = tasksAssigned.employeeID
                  )
                  left outer JOIN times ON (
                    times.jobID = jobs.JobNumber
                    AND times.subjob = subjobs.Number
                    And times.task = tasks.Number
                    And times.employeeID = tasksAssigned.employeeID
                  )
                  WHERE delivAssigned.employeeID = '" . $_SESSION['login_user'] . "'
                  AND jobs.JobNumber = '$req' "
              ;
      $newresult = $conn->query($newsql);
      echo "<v-list>";
      if ($newresult->num_rows > 0){ // If has deliverables/milestones, print them    
        // Display each fetched row
        
        $newfields = array("SubjobName","SubjobNumber");

        while ($newrow = $newresult->fetch_array()){
          $newfield = $newfields[0];
          $num = $newfields[1];
          echo "<v-list-tile>     
          <v-btn class =\"nodecor\" flat onclick = \"filter('$newrow[$num]','$req')\">
          <span>";
          echo $newrow[$newfield];
          echo "</span>
          </v-btn>
          </v-list-tile>";
        }
        
      // Print Out View Button Column
      }
      else{
        echo "<v-list-tile> No subjobs found </v-list-tile>";
      }  
    
      echo "
      </v-list>
      </v-menu>   
      </v-list-tile>";
    }

     
  }
  else{ // is an error
    // End Table Early
    echo "<br>";

    // Display Error Message
    echo "<center>";
    echo "<strong>";
    echo "<font size='10'>";
    if ($result === FALSE){
      echo "Sign";
    }
    else{
      echo "No Jobs found";
    }
    echo "</font>";
    echo "</strong>";
    echo "</center>";
  }

?>

 


      </v-list-item-content>
    </v-list-item>
  </v-list>
  </v-list-item>
</v-navigation-drawer>


      <v-content>
        
<h1 style="color: cornflowerblue">
<v-toolbar-side-icon color = "#6495ED" class = "white--text" @click = "drawer = !drawer"></v-toolbar-side-icon>
 Hi, <?php echo $_SESSION['login_user']; ?>
</h1>
<div style="text-align: center;"> 
  <button  href = "#">
  Home
</button > <big>/ </big>
<button  href = "#">
  Milestone
</button >
 </div>
<div></div>
<div></div>

<h2 style="color: cornflowerblue">
  Milestone
</h2>

<!-- Generate Table of Employee's Deliverables/Milestones -->
<?php

if (session_id() == ''){
 session_start();
}

/*
Function: queryEmployee

Action: Queries and Displays All Milestones An Employee is Assigned
Input: Employee ID From _SESSION['login_user']
Output: Deliverables Assigned to Given Employee
Request: Buttons Redirect to Milestone Communication
Return: None
*/
//function queryEmployee(){
  // Connect to DB
  $username = $_SESSION['login_user'];
  if(!$username) {
    http_response_code(401); die("bad username");
  }

  $conn = getConn();
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Generate SQL Query
  $sql = "SELECT
            jobs.Name AS ProjectName,
            jobs.Manager AS Manager,
            subjobs.Name AS SubjobName,
            deliv.Name AS DeliverableName,
            tasksAssigned.hours AS TimeAllocated,
            times.hours AS TimeSpent,
            times.hours - tasksAssigned.hours AS Variance,
            delivAssigned.Status AS Status,
            deliv.deliverableID as delivID,
            jobs.JobNumber AS JobNumber,
            subjobs.Number AS SubjobNumber
          FROM jobs
          left outer JOIN subjobs
            ON subjobs.jobID = jobs.JobNumber
          left outer  JOIN tasks ON (
            tasks.Job = jobs.JobNumber
            AND tasks.Subjob = subjobs.Number
          )
          left outer JOIN tasksAssigned ON (
            tasksAssigned.jobID = jobs.JobNumber
            AND tasksAssigned.subjobID = subjobs.Number
            And tasksAssigned.taskID = tasks.Number
          )
          left outer JOIN deliverables AS deliv ON (
            deliv.jobID = jobs.JobNumber
            AND deliv.subjobID = subjobs.Number
            And deliv.taskID = tasks.Number
          )
          left outer JOIN deliverableAssigned as delivAssigned ON (
            delivAssigned.deliverableID = deliv.deliverableID
            AND delivAssigned.employeeID = tasksAssigned.employeeID
          )
          left outer JOIN times ON (
            times.jobID = jobs.JobNumber
            AND times.subjob = subjobs.Number
            And times.task = tasks.Number
            And times.employeeID = tasksAssigned.employeeID
          )
          WHERE delivAssigned.employeeID = '" . $_SESSION['login_user'] . "';"
  ;
            
  // Query Database
  $result = $conn->query($sql);

  // Print Results to Webpage
  echo "<table class=\"center\" style=\"width:80%\" >
    <tr>
    <th>Project Name</th>
    <th>My Manager</th> 
    <th>Subjob</th>
    <th>Milestone</th>
    <th>Time Allocated</th>
    <th>Time Spent</th>
    <th>Variance</th>
    <th>Status</th>
    <th>View</th>
    <th>Action</th>
    </tr>";

  if ($result->num_rows > 0){ // If has deliverables/milestones, print them
    $fields = array("ProjectName","Manager","SubjobName","DeliverableName","TimeAllocated","TimeSpent","Variance","Status","JobNumber","SubjobNumber");

    // Display each fetched row
    while ($row = $result->fetch_array()){
      echo "<tr class = \"ProjectName\" name = '". $row['JobNumber']. "'>";

      // Display data fetched for relevant Field
      // Print Out The First 8 Fields Queried In Order Queried
      for ($i = 0; $i < 8; $i++){
        $field = $fields[$i];
          if (strcmp($field, "SubjobName") == 0){
            echo "<td class = \"SubjobName\" name = '". $row['SubjobNumber']."'>";
        } else {
            echo "<td>";
		
      }
        
        // If TimeSpent Exceeds Time Allocated, Display the Variance in Red.
        if (strcmp($field, "Variance") == 0 and $row[$field] > 0){ 
          echo "<font color = \"red\">" . $row[$field] . "</font>";
        }
        else if (strcmp($field, "Manager") == 0){ // Display Manager in Blue
          echo "<font color = \"blue\">" . $row[$field] . "</font>";
        }
        else{ // Other Fields Have Default Colour
          echo $row[$field];
        }
        echo "</td>";
      }

      // Print Out View Button Column
      echo "<td>";
        echo "<v-btn small dark color=\"#6495ED\" href=\"./submission.php?deliverable_id=" . $row["delivID"] . "&employee_id=" . $_SESSION["login_user"] . "\">"; // For Button
          echo "View";
        echo "</v-btn>";
      echo "</td>";

      // Print Out Action Button Column
      echo "<td>";
      if (strcmp($row["Status"], "Accepted") == 0){
        // No Button For Accepted
      }
      else{
        echo "<v-btn small dark color=\"#6495ED\" href=\"./submission.php?deliverable_id=" . $row["delivID"] . "&employee_id=" . $_SESSION["login_user"] . "\">"; // For Button
        if (strcmp($row["Status"], "Unsubmitted") == 0){
          echo "Send";
        }
        else if (strcmp($row["Status"], "Rejected") == 0){
          echo "Revise";
        }
        else if (strcmp($row["Status"], "Seen") == 0 || strcmp($row["Status"], "Sent") == 0){
          echo "Resend";
        }
        echo "</v-btn>";
      }
      echo "</td>";

      echo "</tr>";
    }
    echo " </table>";
  }
  else{ // is an error
    // End Table Early
    echo "</table>";

    // Display Error Message
    echo "<center>";
    echo "<strong>";
    echo "<font size='10'>";
    if ($result === FALSE){
      echo "Database Connection Failed, Please Check Your Connection To The Database";
    }
    else{
      echo "No Milestones or Deliverables Found";
    }
    echo "</font>";
    echo "</strong>";
    echo "</center>";
  }

?>

  <div mt-2 style="margin-left:10%" hidden> 
      <v-btn small dark color="#6495ED">Previous</v-btn> 
        <v-btn small dark color="#6495ED">Next</v-btn>
    </div>
</v-container>
</v-parallax>
   
     </v-content>
    </v-container>
    </v-app>
  </div>
 

  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js"></script>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/vuetify/1.5.16/vuetify.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vuetify/1.5.16/vuetify.js.map"></script>
 

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


<script>
  $.holdReady(true)

document.addEventListener("DOMContentLoaded", () => {
new Vue({
el: "#app",
data(){
  return{
    drawer:false
  }
},
mounted() {
  $.holdReady(false)
}
})
});
</script>

</body>
</html>

