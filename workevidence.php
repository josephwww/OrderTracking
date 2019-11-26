<?php
    include 'func.php';
    session_start();
	
    if (!isset($_SESSION['login_user'])) {
        http_response_code(401);
		echo "<script language=javascript>alert ('login first');</script>"; 
        $_SESSION['userurl'] = $_SERVER['REQUEST_URI']; 
        echo '<script language=javascript>window.location.href="/index.php"</script>'; 
    }

    if(!isset($_GET['deliverable_id'])||!isset($_GET['employee_id'])) {
        http_response_code(400);
		die ("Illegal page. Load in from Tracking Progress!<br><a href=\"/index.php\">HOME</a>");
    }

    else if(!$_SESSION['isAdmin']&&!isRelevant($_SESSION['login_user'],$_GET['deliverable_id'])) {
        http_response_code(401); echo "Permission denied.\n";
    }

    $deliverable_id=$_GET['deliverable_id'];
	$employee_id=$_GET['employee_id'];

	/*
	If user presses Save, the text in task outline is saved to the database under deliverables 
	*/
	if($_POST['Save']) {

    $taskOutline = $_POST['taskOutline'];

    $sqlConnect = getConn();

    $sqlSaved="UPDATE deliverables SET taskOutline ='".$taskOutline."' WHERE deliverableID='".$deliverable_id."'";

    $insertinto=$sqlConnect -> query($sqlSaved);
  }
	
	/*
	If user presses Save, the text in the stage box and percentage box is added in a new row to the database under stagesofProgress
	*/
    if($_POST['Save_Stage']) {

    $stage = $_POST['stagetoadd'];

	$percentage=$_POST['pertoadd'];
	
	echo var_dump($_POST['pertoadd']);
    $sqlConnect = getConn();

    $sqlstage="Insert into stagesofProgress (Stage,Percentage) VALUES('".$stage."' ,'".$percentage."') ";
	
    $insertinto2=$sqlConnect -> query($sqlstage);
		

    }
	

	/*
	If user presses Save, the text in notes is saved to the database with the corresponding stage 
	*/
	if($_POST['Save_Note']) {

    $stage2 = $_POST['stage2'];

	$note=$_POST['note'];

    $sqlConnect = getConn();

    $sqlnote="UPDATE stagesofProgress SET Notes ='".$note."' where Stage='".$stage2."' ";

    $insertinto3=$sqlConnect -> query($sqlnote);

    }
	
	/*
	If user presses current stage, the current time and date is saved to the stagesofProgress table with that corresponding stage. 
	Also adds the percentage to the deliverables table.
	*/
	if(isset($_GET['current_stage'])){
		
	$current_stage=$_GET['current_stage'];
	$percentage=$_GET['percentage'];
	
	$sqlConnect = getConn();
	
	$sqlinsert="UPDATE stagesofProgress Set currentDate = now() where Stage='".$current_stage."'";
	$sqldeliverables="Update deliverables set percentCompleted ='".$percentage."' where deliverableID='".$deliverable_id."'";
	
	$result=$sqlConnect -> query($sqlinsert);
	$result2=$sqlConnect -> query($sqldeliverables);
	}
	
	/*
	If user presses Save, the employeeid and deliverableid is added to the submissions table
	*/
	if($_POST['Save_add']) {
		
	$worker=$_POST['Worker'];
	
	$sqlConnect = getConn();
	
	$sqlinsert="INSERT into submissions (deliverableID,employeeID) VALUES ('".$deliverable_id."' ,'".$worker."') ";
	
	$result=$sqlConnect -> query($sqlinsert);
	}


?>

<!DOCTYPE html>

<html>


<head>

  <style>
.button {
  background-color: #6495ED; /* Blue */
  border: none;
  color: white;
  padding: 6px 7px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 12px;
  margin: 1px 1px;
  cursor: pointer;
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
	
	div.fixed{
		position:fixed;
		bottom:100
		right:200
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

              <v-btn style = "text-decoration: None" href = "#" text>

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

  Milestone <br> 
  <?php echo "<a href='/TrackingProgress.php?deliverable_id=".$_GET['deliverable_id']."&employee_id=".$_GET['employee_id']."' class='button2' aria-label='Left Align' name='tracking_progress' value='tracking_progress'>Tracking Progress</a></button>"?>
 <font size="2">/    Work Evidence</font>
</h2>

<table class="center" style="width:80%" >



    <tr>

        <th colspan="11"><?php getDeliverableTitle($deliverable_id,$employee_id);?></th>

    </tr>

    <tr>

      <th>Worker</th>

      <th>Milestone</th>

      <th>Subjob</th>

      <th>Role</th>

      <th>Deadline</th>

      <th>Prerequisite of</th>

      <th>Status</th>

      <th>View</th>

      <!--<th>Action</th>-->



    </tr>

	

	<?php

	// Connect to DB

    

    $deliverable_id=$_GET['deliverable_id'];

    $employee_id=$_GET['employee_id'];



	$sqlConnect = getConn();
	
	//Query to find the name of the deliverable which is a prerequisite to the current one
	$prerequisitequery="select distinct D.name from deliverables D inner join preRequisites 
	on parentDeliverableID=D.deliverableID 
	where childDeliverableID=".$deliverable_id;
	
	$prerequisite=$sqlConnect->query($prerequisitequery);
	$rowP=$prerequisite->fetch_assoc();
	
	//Query to find the columns required for the milestone table 
	$sql= "SELECT distinct DA.employeeID,D.Name,S.name,T.role,J.DueDate, status 
	FROM `submissions` DA inner join deliverables D on D.deliverableID=DA.deliverableID 
	inner join subjobs S on S.Number=D.subjobID inner join tasksAssigned T on T.taskID=D.taskID 
	 inner join jobs J on J.JobNumber=D.jobID 
	WHERE DA.deliverableID= ".$deliverable_id;


      $result = $sqlConnect->query($sql);

      if($result===false){ die("query error"); }

//Loop to show the data from the database queries in a table 
if ($result->num_rows > 0){

     while($row = $result->fetch_assoc()) { 

       echo "<tr>";

         echo "<td>" . $row["employeeID"] . "</td>";

         echo "<td>" . $row["Name"]       . "</td>";

         echo "<td>" . $row["name"]       . "</td>";

         echo "<td>" . $row["role"]       . "</td>";

         echo "<td>" . $row["DueDate"]    . "</td>";
		 
		 echo "<td>" . $rowP["name"]   . "</td>";

         echo "<td>" . $row["status"]     . "</td>";



         // Print Out View Button Column which sends the user to the new submissions page for the corresponding deliverableid and employeeid 

         echo "<td>";

           echo "<v-btn small dark color=\"#6495ED\" href=\"/submission.php?deliverable_id=" .$deliverable_id. "&employee_id=" . $employee_id . "\">";  // For Button

             echo "View";

           echo "</v-btn>";

         echo "</td>";

       echo "</tr>";
    }
}


	?>
  </table>
  <script>



  var node=document.getElementsByClassName("variance");



for(i in node) {

  var value=node[i].innerHTML;

  if (value[0]!='-') {



    node[i].style.color = 'red';
  }

}

  </script>

  <div class="pt-5 pl-50">

	<form action="<?php echo "/workevidence.php?deliverable_id=".$_GET['deliverable_id']."&employee_id=".$_GET['employee_id'];?>"id="workevAction3" name="workevAction3" method="POST" >
	<?php if(!$_POST['add1']){
	echo "<v-btn large dark color='#6495ED'><input type='submit' name='add1' value='Add'/></v-btn>";
	}
	?>
	
	<?php if($_POST['add1']){
	echo "   
	<v-textarea 
    outlined
    name=\"Worker\"
	label=\" Add Worker\"
	form=\"workevAction3\"
    value=\"\"
	rows=\"1\" cols=\"1\"></v-textarea>
	
	<v-btn large dark color='#6495ED'><input type='submit' name='Save_add' value='Save Worker'/></v-btn>"
	 ;}?>
	 
	 </form>

  </div>

 <div class="mt-10">

<v-row>

     <v-col cols="8" md="4">



   <v-textarea

    outlined

    name="taskOutline"

	label="Tasks Outline"

	form="workevAction"

    value="<?php getTextareaTask($deliverable_id);?>"



></v-textarea>

	<form action="<?php echo "/workevidence.php?deliverable_id=".$_GET['deliverable_id']."&employee_id=".$_GET['employee_id'];?>" id="workevAction" name="workevAction" method="POST" >

    <div class="text-center">

	<?php echo"<v-btn dark color='#6495ED'><input type='submit' name='Save' value='Save'/></v-btn>";?>
	
	
	</div>

	</form>

     </v-col>
	 
	 
	      <v-col cols="8" md="4">

        <h2 style="color: cornflowerblue">

            <small><Small>

          <?php getTaskName($deliverable_id);?> 

          </small></small>

            </h2>

		
        <table>

            <tr>  

              <th>Time Allocated</th> <td><?php getTimeAllocated($deliverable_id);?></td>

              

            </tr>

             <th>Time spent</th> <td><?php getTimeSpent($deliverable_id);?></td>

            <tr>

              <tr>

                <th>Variance</th> <td><?php getVariance($deliverable_id);?></td>

              </tr>

            </table>

      </v-col>


     <v-col cols="8" md="4">

         <table>

           <tr>



            <th colspan="2">Role Requirements</th>

            </tr>

             <tr>
                 <th>Role</th>

                 <th>Certification Needed</th>

             </tr>

            <tr>

                <td>Snr Electrician</td>

                <td>[qualification list]</td>



             </tr>



         </table>



     <div class="text-right mt-3">


  <v-btn dark color='#6495ED'>

    <!<a style="color: cornflowerblue;">

      Add Role

    <!</a>

  </v-btn>


  </div>


    </v-col>


    </v-row>


  </div>


 <!-- -->
    
     </v-col>



     <v-col cols="12" md="6">



         <table>



           <tr>



            <th colspan="4">Stages of Progress</th>

            </tr>

             <tr>

				

				<th>Current Stage</th>

                 <th>Stage</th>



                 <th>Percentage</th>

				 

				 <th>Notes</th>


             </tr>

			<?php

	

	// Connect to DB

    $deliverable_id=$_GET['deliverable_id'];

    $employee_id=$_GET['employee_id'];



	$sqlConnect = getConn();

	//Query to select columns for stages of progress table 
    $sql= "SELECT  distinct Stage,Percentage,Notes from stagesofProgress";

        

    $result = $sqlConnect->query($sql);
	
    if($result===false){ die("query error"); }

?>	  
 

<?php 
if ($result->num_rows > 0){
?><form action="<?php echo "/workevidence.php?deliverable_id=".$_GET['deliverable_id']."&employee_id=".$_GET['employee_id'];?>"id="workevAction1" name="workevAction1" method="POST" >
		<?php
	//loop to print out results in table 
     while($row = $result->fetch_assoc()) {		
       echo "<tr><td>"."<v-btn small dark color=\"#6495ED\" href='/workevidence.php?deliverable_id=".$_GET['deliverable_id']."&employee_id=".$_GET['employee_id']."&current_stage=".$row['Stage']."&percentage=".$row['Percentage']."' name='current_stage' value='current_stage'>Set Stage</v-btn></td>".
		"</td><td>".$row["Stage"].
		"</td><td>".$row["Percentage"].
		"</td><td>".$row["Notes"]."</td></tr>";

    }

} 
//
echo"</form>";

	?>	 

         </table>

	</v-col>	

	 <form action="<?php echo "/workevidence.php?deliverable_id=".$_GET['deliverable_id']."&employee_id=".$_GET['employee_id'];?>"id="workevAction2" name="workevAction2" method="POST">

	<?php if(!$_POST['Add_Stage']){ 
	echo "<v-btn large dark color='#6495ED'><input type='submit' name='Add_Stage' value='Add Stage'/></v-btn>";}?>
	
	
	 <?php if($_POST['Add_Stage']){
		echo "   
	<v-textarea
    outlined
    name=\"stagetoadd\"
	label=\"Stage\"
	form=\"workevAction2\"
    value=\"\"
	></v-textarea>
	
	<v-textarea
    outlined
    name=\"pertoadd\"
	label=\"Percentage\"
	form=\"workevAction2\"
    value=\"\"
	></v-textarea>
	
	<v-btn large dark color='#6495ED'><input type='submit' name='Save_Stage' value='Save Stage and Percentage'/></v-btn>"
	 ;}?>


	  <?php if(!$_POST['add_note']){ 

     echo "<v-btn large dark color='#6495ED'><input type='submit' name='add_note' value='Add Note'/></v-btn>";} ?>

	 <?php if($_POST['add_note']){
		echo "   
	<v-textarea
    outlined
    name=\"stage2\"
	label=\"Stage\"
	form=\"workevAction2\"
    value=\"\"
	></v-textarea>
	
	<v-textarea
    outlined
    name=\"note\"
	label=\"Note\"
	form=\"workevAction2\"
    value=\"\"
	></v-textarea>
	
	<v-btn large dark color='#6495ED'><input type='submit' name='Save_Note' value='Save Note'/></v-btn>"
	 ;}?>

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



