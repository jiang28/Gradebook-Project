<table>
<thead>
<tr><td>Username</td><td>Name</td><td>Role</td><td>Actions</td></tr>
</thead>
<tbody>
<head>

</head>
<body>
	

<?php 


	$staff = db_query('select Staff_id,Name,role from Staff where role!=\'Admin\'');

foreach ($staff as $row) {
  echo '<tr><td>' .$row['Staff_id'].'</td>';
  echo 
'<td>' .$row['Name'].'</td>';
  echo '<td>' .$row['role'].'</td>';
  echo '<td><button onclick="window.location.href += \'' . $row['Staff_id'] . '/\'" class="small edit"><span>Edit</span></button>';
  $staff_id=$row['Staff_id'];
  $param=0;
  ?>


 
  

<button onclick="submitUpdate({Old_Staff_id: '<?php echo $staff_id; ?>',param: '<?php echo $param;?>'})" class="small delete"><span>Delete</span></button></td></tr>
  <?php
 # echo '\n';
} ?>

<?php 
	if(count($args) == 1)
	{
	
	  

 $StaffUpdate=db_query('Select Staff_id,Name,role from Staff where Staff_id =?',$args[0]);

 $param=1;
				
?>		
	     
<input id="Staff_id" type="text" value="<?php echo $StaffUpdate[0]['Staff_id']; ?>" />
		<input id="Name" type="text" value="<?php echo $StaffUpdate[0]['Name']; ?>" />
		 <input id="Role" type="text" value="<?php echo $StaffUpdate[0]['role']; ?>" />

   <button onclick="submitUpdate({Staff_id: $('#Staff_id').val(),Name:$('#Name').val(),Role: $('#Role').val(),param: '<?php echo $param;?>',Old_Staff_id: '<?php echo $StaffUpdate[0]['Staff_id'];?>'})">Save</button>
   <button onclick="window.location.href='..'">Cancel</button>

</tbody>
</table>
</body>
<?php } ?>
