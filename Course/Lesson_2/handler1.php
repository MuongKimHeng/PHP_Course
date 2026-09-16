<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <?php 
    print_r($_GET); //print with arrays 
    ?>  

    <?php 
    //Method GET :
    // if(isset($_GET['submit'])){
    //     echo 'Username: ' . $_GET['username'].'<br>';
    //     echo 'Password: ' . $_GET['userpass'];    
    // }else {
    //     echo 'No data received.';
    // }

    // Method GET :
    if(isset($_POST['submit'])){
        echo 'Username: ' . $_POST['username'].'<br>';
        echo 'Password: ' . $_POST['userpass'];    
    }else {
        echo 'No data received.';
    }
    ?>

</body>
</html>