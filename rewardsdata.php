<?php

$db_server = "localhost";
$db_username = "root";  
$db_password = "";
$db_name = "rewards_db";
$_conn= "";

try{
    $conn = mysqli_connect($db_server, $db_username, $db_password, $db_name);
}
catch(mysqli_sql_exception){
    echo "Connection failed" ;
}
if (!$conn) {
    echo"connected to server successfully";
}

/*
$sql ="CREATE TABLE rewards (
    reward_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    point_cost INT NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

$sql = "CREATE TABLE point_transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    points_earned INT NOT NULL,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

$sql = "CREATE TABLE reward_redemptions (
    redemption_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    reward_id INT NOT NULL,
    redemption_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    points_spent INT NOT NULL,
    status ENUM('pending', 'fulfilled', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (reward_id) REFERENCES rewards(reward_id)
);"
*/

?>