<?php
$conn = mysqli_connect("localhost", "root", "", "report");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql = "CREATE TABLE problem_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    problem_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    urgency VARCHAR(20) NOT NULL,
    screenshot_path VARCHAR(255),
    created_at DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'pending'
)";

if (mysqli_query($conn,$sql)) {

    "Table created succussdfy";

}else{
    echo"oh ohhhhhhhh idunno misimisi potatooo" . mysqli_error($conn);
}

mysqli_close($conn);
?>