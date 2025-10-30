
<?php  
include '../../config/config.php';

$id = $_POST['datapost']; 
$state = "SELECT * FROM states WHERE country_id = '$id'"; 
$result = mysqli_query($conn, $state);

while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
    <?php
}
?>

 