<?php

 /*
	This project shows how to make use of md5 in a secure way 
	no matter how the same type of the pass word is repeated the crypted 
	result will always be unique
 */
 
 
 
	// Here we make the connection to the database
	$con = mysqli_connect('localhost','root','','mypass');
	
	$err = '';  // this was use to initialize the $err varriable
	
	// we validate the user input in this if block
	if(isset($_POST['submit'])){
		
		$pass = mysqli_real_escape_string($con, htmlspecialchars($_POST['pass']));
		
		
		if($pass == ''){
			$err = "You have to input some value to set the password";
			
		}elseif($pass < 6){
			$err = "The password must be greater than 6 characters";
			
		}elseif($pass != ''){
			
			// if all works well we then insert the value into the database
			$md5 = md5($pass);  // the normal encryption using the MD5
			$sql = "INSERT INTO pass_detail SET
					text			= '$pass',
					pass            = '$md5',
					gen_pass 		=  '',
					created_date    =  now(),
					updated_date    =  now()";
			
			$query = mysqli_query($con, $sql) or die(mysqli_error($con));
			$last_id = mysqli_insert_id($con); // we get the id of the newly created password
			
			$last_id_crypted = md5($last_id);
			
			
			/////////////    This is where the Logic is Done
			$new_gen = $last_id_crypted.''.$md5.''.$last_id_crypted;
			//////////////////////////////////////////////////////////////////////////////////
			
			
			$sql_update = "UPDATE pass_detail SET
						   gen_pass   =  '$new_gen'
						   WHERE id = $last_id";
			$query_updated = mysqli_query($con, $sql_update);
			
		}
		
	}
	?>
	
<html>
	<head>
		<title> Unique Password</title>
		<link rel="stylesheet" href="css/bootstrap.css"/>
		<link rel="stylesheet" href="css/styles.css"/>
		<style>
			
		</style>
	</head>
	<body>
		<div class="container">
			<div class="jumbotron">
				<center>
				<h2 style="text-transform: uppercase;"> Getting a unique Encrypted Value with MD5</h2>
					<div class="pass_box">
						<form method="post" action="">
							
							<input type="password" name="pass" class="enter" placeholder="Set The Password" /> 
							<input type="submit" class="submit" name="submit" value="Create " />
							
						</form>
						<!-- We use this to display error message if there is any -->
						<?php if($err != ''){
							echo '<div class="alert alert-warning" style="font-size: 18px;">'. $err .'</div>';
						}?>
					</div>
				</center>
			</div>
			
			<table class="table table-condensed table-bordered table-striped table-hover ">
				<tr>
					<th> S/N </th>
					<th> Password in Text </th>
					<th> Password with MD5 </th>
					<th> Password fully secured </th>
				</tr>
				<?php 
					$result = '';
					$sql = mysqli_query($con, "SELECT * FROM pass_detail") or mysqli_error($con);
					$row = mysqli_num_rows($sql);
					
					// Over here we will check if there is any value in the database
					if($row > 0){
						
						// We use the While loop to get the entire value into the varriable
						while($fetch = mysqli_fetch_assoc($sql)){
							$tableId = $fetch['id'];
							$pureText = $fetch['text'];
							$venPass = $fetch['pass'];
							$securePass = $fetch['gen_pass'];
				?>		

					<tr>				
						<td> <?php echo $tableId ?> </td>
						<td> <?php echo $pureText ?> </td>
						<td> <?php echo $venPass ?> </td>
						<td> <?php echo $securePass ?> </td>
					</tr>					
				<?php				
						}
					}else{
						$result = "No Password Created";
					}
				?>
			</table>

		</div>
	</body>
</html>