
<?php  
include '../../config/config.php';

$id = $_POST['datapost']; 
$city = "SELECT * FROM cities WHERE state_id = '$id'";
$result = mysqli_query($conn, $city);

while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
    <?php
}
?>

 