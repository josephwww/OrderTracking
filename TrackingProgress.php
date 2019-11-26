
<!DOCTYPE html>
<html>
<head>

  <style>
    table {
      font-family: arial, sans-serif;
      border-collapse: collapse;
      width:80%; 
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

    .Collapsible:before {
      content: "\229F";
    }

    .Collapsed:before {
      content: "\229E";
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
    include "func.php";
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
              <v-toolbar-side-icon color = "#6495ED" class = "white--text" @click = "drawer = !drawer"></v-toolbar-side-icon>

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

// Insert Sidebar Code

?>

      </v-list-item-content>
    </v-list-item>
  </v-list>
</v-navigation-drawer>

<v-container>
      <v-content>
<h1 style="color: cornflowerblue">
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

/*
Function: printNonDelivRow

Action: For Non Deliverable Rows, ie Jobs, Subjobs and Tasks, prints an Queried SQL Record In Table Form
Input: $row: Queried SQL Record,
       $depth: Depth of Row: 1 -> Job, 2 -> Subjob, 3 -> Task, 4 -> Deliverable
       $hasPreReq Queried Parent Prerequisite
Output: HTML Table Row of SQL Record Data
Return: None
 */
function printNonDelivRow($row, $depth, $hasPreReq){
  $fields = array("ProjectName","SubjobName","TaskName","DeliverableName","Manager","TimeAllocated","TimeSpent","Variance","Deadline","Has Prerequisite","Status");
  $numFields = sizeof($fields);

  // Display each fetched row
  echo "<tr class=\"Collapsible\" data-depth=$depth data-timesHidden=0>";

  // Display data fetched for relevant Field
  // Print Out The Queried Fields In Order Queried
  for ($i = 0; $i < $numFields; $i++){
    $field = $fields[$i];
    
    echo "<td>";

    // If TimeSpent Exceeds Time Allocated, Display the Variance in Red.
    if (strcmp($field, "Variance") == 0 and $row[$field] > 0){ 
      echo "<font color = \"red\">" . $row[$field] . "</font>";
    }
    else if (strcmp($field, "Manager") == 0){ // Display Manager in Blue
      echo "<font color = \"blue\">" . $row[$field] . "</font>";
    }
    else if (strcmp($field, "Has Prerequisite") == 0){ // Display Prerequisite from Function Parameter
      echo $hasPreReq;
    }
    else{ // Other Fields Have Default Colour
      echo $row[$field];
    }

    echo "</td>";
  }

  // Print Out View Button Column
  echo "<td>";
  echo "</td>";

  //echo "</div>"; // Close Row
  echo "</tr>";
}

// Generate SQL Query
$jobSQL = "SELECT
  jobs.Name AS ProjectName,
  '' AS SubjobName,
  '' As TaskName,
  '' AS DeliverableName,
  jobs.Manager AS Manager,
  sum(tasksAssigned.hours) AS TimeAllocated,
  sum(times.hours) AS TimeSpent,
  sum(times.hours - tasksAssigned.hours) AS Variance,
  jobs.DueDate AS Deadline,
  '' AS HasPrerequisite,
  '' AS Status,
  jobs.JobNumber AS JobID
  FROM jobs
  LEFT OUTER JOIN subjobs
  ON subjobs.jobID = jobs.JobNumber
  JOIN tasks ON (
    tasks.Job = jobs.JobNumber
    AND tasks.Subjob = subjobs.Number
  )
  LEFT OUTER JOIN tasksAssigned ON (
    tasksAssigned.jobID = jobs.JobNumber
    AND tasksAssigned.subjobID = subjobs.Number
    And tasksAssigned.taskID = tasks.Number
  )
  LEFT OUTER JOIN deliverables AS deliv ON (
    deliv.jobID = jobs.JobNumber
    AND deliv.subjobID = subjobs.Number
    And deliv.taskID = tasks.Number
  )
  LEFT OUTER JOIN submissions as subm ON (
    subm.deliverableID = deliv.deliverableID
    AND subm.employeeID = tasksAssigned.employeeID
  )
  LEFT OUTER JOIN times ON (
    times.jobID = jobs.JobNumber
    AND times.subjob = subjobs.Number
    And times.task = tasks.Number
    And times.employeeID = tasksAssigned.employeeID
  )
  GROUP BY jobs.JobNumber"
  ;

// Query Database For Job Data
$jobResult = $conn->query($jobSQL);

// Print Results to Webpage
echo "<table>
  <thead>
    <tr>
      <td>Expand</td>
      <td>Project Name</td>
      <td>Subjob</td>
      <td>Task</td>
      <td>Milestone</td>
      <td style=\"width:20%\">My Manager</td> 
      <td>Time Allocated</td>
      <td>Time Spent</td>
      <td>Variance</td>
      <td>Deadline</td>
      <td>Has Prerequisite</td>
      <td>Status</td>
      <td>View</td>
    </tr>
  </thead>";

/**** Job Rows ****/
if ($jobResult->num_rows > 0){ // If has deliverables/milestones, print them

  while ($jobRow = $jobResult->fetch_array()){
    printNonDelivRow($jobRow, 1, ""); // Jobs are Depth 1 and Jobs have no prerequisites

    /*** Subjob Rows ***/
    // Generate SubJob Query
    $subjobSQL = "SELECT
      '' AS ProjectName,
      subjobs.Name AS SubjobName,
      '' As TaskName,
      '' AS DeliverableName,
      '' AS Manager,
      sum(tasksAssigned.hours) AS TimeAllocated,
      sum(times.hours) AS TimeSpent,
      sum(times.hours - tasksAssigned.hours) AS Variance,
			'' AS Deadline,
      '' AS Status,
      subjobs.Number AS SubjobID
      FROM jobs
      LEFT OUTER JOIN subjobs
      ON subjobs.jobID = jobs.JobNumber
      JOIN tasks ON (
        tasks.Job = jobs.JobNumber
        AND tasks.Subjob = subjobs.Number
      )
      LEFT OUTER JOIN tasksAssigned ON (
        tasksAssigned.jobID = jobs.JobNumber
        AND tasksAssigned.subjobID = subjobs.Number
        And tasksAssigned.taskID = tasks.Number
      )
      LEFT OUTER JOIN deliverables AS deliv ON (
        deliv.jobID = jobs.JobNumber
        AND deliv.subjobID = subjobs.Number
        And deliv.taskID = tasks.Number
      )
      LEFT OUTER JOIN submissions as subm ON (
        subm.deliverableID = deliv.deliverableID
        AND subm.employeeID = tasksAssigned.employeeID
      )
      LEFT OUTER JOIN times ON (
        times.jobID = jobs.JobNumber
        AND times.subjob = subjobs.Number
        And times.task = tasks.Number
        And times.employeeID = tasksAssigned.employeeID
      )
      where jobs.jobNumber = '" . $jobRow["JobID"] . "'
      GROUP BY jobs.JobNumber, subjobs.Number, subjobs.Name"
  ;

    // Query Database For Subjob Data
    $subjobResult = $conn->query($subjobSQL);

      // Display each fetched row
      while ($subjobRow = $subjobResult->fetch_array()){

        // Get Prerequitsite for each subjob
        $subjobPrereqQuery = 
          "SELECT subjobs.Name AS PreReq
          FROM subjobs 
          JOIN preRequisites preReq
            ON (
              preReq.jobID = subjobs.jobID
              AND preReq.parentSubjob = subjobs.Number
            )
          WHERE preReq.jobID = '" . $subjobRow["JobID"] . "' 
            AND preReq.childSubjob = '" . $subjobRow["SubjobID"] . "'";

        $subjobPrereqResult = $conn->query($subjobPrereqQuery);

        $subjobPrereqData = ""; // Initialise Prerequisite Data

        if ($subjobPrereqResult->num_rows > 0){ // If has a parent prerequisite, update data
          $subjobPrereqRow = $subjobPrereqResult->fetch_assoc();
          $subjobPrereqData = $subjobPrereqRow["PreReq"];
        }

        // Print Row
        printNonDelivRow($subjobRow, 2, $subjobPrereqData);

        /** Task Rows **/
        // Generate Task Query
        $taskSQL = "SELECT
          '' AS ProjectName,
          '' AS SubjobName,
          tasks.Name As TaskName,
          '' AS DeliverableName,
          '' AS Manager,
          sum(tasksAssigned.hours) AS TimeAllocated,
          sum(times.hours) AS TimeSpent,
          sum(times.hours - tasksAssigned.hours) AS Variance,
					'' AS Deadline,
          '' AS Status,
          jobs.JobNumber AS JobID,
          subjobs.Number AS SubjobID,
          tasks.Number AS TaskID
          FROM jobs
          LEFT OUTER JOIN subjobs
          ON subjobs.jobID = jobs.JobNumber
          LEFT OUTER JOIN tasks ON (
            tasks.Job = jobs.JobNumber
            AND tasks.Subjob = subjobs.Number
          )
          LEFT OUTER JOIN tasksAssigned ON (
            tasksAssigned.jobID = jobs.JobNumber
            AND tasksAssigned.subjobID = subjobs.Number
            AND tasksAssigned.taskID = tasks.Number
          )
          LEFT OUTER JOIN deliverables AS deliv ON (
            deliv.jobID = jobs.JobNumber
            AND deliv.subjobID = subjobs.Number
            AND deliv.taskID = tasks.Number
          )
          LEFT OUTER JOIN submissions as subm ON (
            subm.deliverableID = deliv.deliverableID
            AND subm.employeeID = tasksAssigned.employeeID
          )
          LEFT OUTER JOIN times ON (
            times.jobID = jobs.JobNumber
            AND times.subjob = subjobs.Number
            AND times.task = tasks.Number
            AND times.employeeID = tasksAssigned.employeeID
          )
          WHERE jobs.jobNumber = '" . $jobRow["JobID"] . "'
            AND subjobs.Number = '" . $subjobRow["SubjobID"] . "'
          GROUP BY jobs.JobNumber, subjobs.Number, tasks.Number, tasks.Name"
  ;

    // Query Database For Task Data
    $taskResult = $conn->query($taskSQL);

      // Display each fetched row
      while ($taskRow = $taskResult->fetch_array()){

        // Get Prerequitsite for each task
        $taskPrereqQuery = 
          "SELECT tasks.Name AS PreReq
          FROM tasks 
          JOIN preRequisites preReq
            ON (
              preReq.jobID = tasks.Job
              AND preReq.parentSubjob = tasks.subjob
              AND preReq.parentTask = tasks.Number
            )
          WHERE preReq.jobID = '" . $taskRow["JobID"] . "' 
            AND preReq.childSubjob = '" . $taskRow["SubjobID"] . "'
            AND preReq.childTask = " . $taskRow["TaskID"];

        $taskPrereqResult = $conn->query($taskPrereqQuery);

        $taskPrereqData = ""; // Initialise Prerequisite Data

        if ($taskPrereqResult->num_rows > 0){ // If has a parent prerequisite, update data
          $taskPrereqRow = $taskPrereqResult->fetch_assoc();
          $taskPrereqData = $taskPrereqRow["PreReq"];
        }

        // Print Row
        printNonDelivRow($taskRow, 3, $taskPrereqData);

        /** Deliverable Rows **/
        // Generate Deliverable Query
        $delivSQL = "SELECT
          '' AS ProjectName,
          '' AS SubjobName,
          '' As TaskName,
          deliv.Name AS DeliverableName,
          '' AS Manager,
					'' AS Deadline,
          '' AS TimeAllocated,
          '' AS TimeSpent,
          '' AS Variance,
          subm.Status AS Status,
          deliv.deliverableID AS delivID
          FROM jobs
          LEFT OUTER JOIN subjobs
          ON subjobs.jobID = jobs.JobNumber
          LEFT OUTER JOIN tasks ON (
            tasks.Job = jobs.JobNumber
            AND tasks.Subjob = subjobs.Number
          )
          LEFT OUTER JOIN tasksAssigned ON (
            tasksAssigned.jobID = jobs.JobNumber
            AND tasksAssigned.subjobID = subjobs.Number
            And tasksAssigned.taskID = tasks.Number
          )
          LEFT OUTER JOIN deliverables AS deliv ON (
            deliv.jobID = jobs.JobNumber
            AND deliv.subjobID = subjobs.Number
            And deliv.taskID = tasks.Number
          )
          LEFT OUTER JOIN submissions as subm ON (
            subm.deliverableID = deliv.deliverableID
            AND subm.employeeID = tasksAssigned.employeeID
          )
          WHERE jobs.jobNumber = '" . $jobRow["JobID"] . "'
            AND subjobs.Number = '" . $subjobRow["SubjobID"] . "'
            AND tasks.Number = " . $taskRow["TaskID"]
          ;

    // Query Database For Deliverable Data
    $delivResult = $conn->query($delivSQL);

      // Display each fetched row
      while ($delivRow = $delivResult->fetch_array()){
        // Get Prerequitsite for each task
        $delivPrereqQuery = 
          "SELECT deliv.Name AS PreReq
          FROM deliverables deliv 
          JOIN preRequisites preReq
            ON preReq.parentDeliverableID = deliv.deliverableID
          WHERE childDeliverableID = " . $delivRow["delivID"];

        $delivPrereqResult = $conn->query($delivPrereqQuery);

        $delivPrereqData = ""; // Initialise Prerequisite Data

        if ($delivPrereqResult->num_rows > 0){ // If has a parent prerequisite, update data
          $delivPrereqRow = $delivPrereqResult->fetch_assoc();
          $delivPrereqData = $delivPrereqRow["PreReq"];
        }


        echo "<tr data-depth=4 data-timesHidden=0>";

        echo "<td>"; // for Expand Field
        echo "</td>";

        // Display data fetched for relevant Field
        // Print Out The Queried Fields In Order Queried
        $fields = array("ProjectName","SubjobName","TaskName","DeliverableName","Manager","TimeAllocated","TimeSpent","Variance","Deadline","Has Prerequisite","Status");
				$numFields = sizeof($fields);
        for ($i = 0; $i < $numFields; $i++){
          $field = $fields[$i];

          echo "<td>";

          // If TimeSpent Exceeds Time Allocated, Display the Variance in Red.
          if (strcmp($field, "Variance") == 0 and $delivRow[$field] > 0){ 
            echo "<font color = \"red\">" . $delivRow[$field] . "</font>";
          }
          else if (strcmp($field, "Manager") == 0){ // Display Manager in Blue
            echo "<font color = \"blue\">" . $delivRow[$field] . "</font>";
          }
          else if (strcmp($field, "Has Prerequisite") == 0){ // Display Prerequisite from Function Parameter
            echo $delivPrereqData;
          }
          else{ // Other Fields Have Default Colour
            echo $delivRow[$field];
          }

          echo "</td>";
        }

        // Print Out View Button Column
        echo "<td>";
          echo "<v-btn small dark color=\"#6495ED\" href=\"./workevidence.php?deliverable_id=" . $delivRow["delivID"] . "&employee_id=" . $_SESSION["login_user"] . "\">"; // For Button
            echo "View";
          echo "</v-btn>";
        echo "</td>";

        echo "</tr>";

      }
      }
      }
  }
  echo "</table>";
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


  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js"></script>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/vuetify/1.5.16/vuetify.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vuetify/1.5.16/vuetify.js.map"></script>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>


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

<script type="application/javascript">
$(document).ready(
  // Collapse Descendant Rows
  function () {
    // Collapse Descendant Rows
      function collapse() {
        var depth = $(this).attr("data-depth");
        var nx = $(this).next();
        $(this).removeClass("Collapsible");
        $(this).addClass("Collapsed");
        $(this).off();
        $(this).click( expand );

        while (nx.attr("data-depth") > depth){
          if (nx.attr("data-timesHidden") == 0){
            nx.toggle();
          }
          nx.attr("data-timesHidden", parseInt(nx.attr("data-timesHidden")) + 1);

          nx = nx.next();
        }

      }

    // Expand Descendant Rows
      function expand() {
        var depth = $(this).attr("data-depth");
        var nx = $(this).next();
        $(this).removeClass("Collapsed");
        $(this).addClass("Collapsible");
        $(this).off();
        $(this).click( collapse );

        while (nx.attr("data-depth") > depth){
          nx.attr("data-timesHidden", parseInt(nx.attr("data-timesHidden")) - 1);
          if (nx.attr("data-timesHidden") == 0){
            nx.toggle();
          }

          nx = nx.next();
        }

      }

    // Initially Collapse All Rows
    $(".Collapsible").each( collapse );

  }
);
</script>

</body>
</html>

