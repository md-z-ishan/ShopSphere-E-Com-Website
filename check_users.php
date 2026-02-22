<?php
include "db.php";

$sql = "SELECT * FROM user_info";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "User ID: " . $row["user_id"]. " - Name: " . $row["first_name"] . " " . $row["last_name"] . " - Email: " . $row["email"] . "\n";
    }
} else {
    echo "0 results in user_info";
}
?>
