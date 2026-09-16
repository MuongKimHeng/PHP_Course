<?php
declare(strict_type=1);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>


    <?php
    //phpinfo();  
    /* 

    $ = variable
    $name = 'user1';
    $age = 20;

    echo/print() = display or print
    echo $name;
    print "You are <b style=\"color:red\"> $age </b> years old!";
    print 'You are ' . $age . ' years old';

    $a = 9; $b = 8
    $result = a <=> b // <=> = <-1, =0, >1
    echo '<br>Equal?' . $result;

    $role = 'admin';

    // Switch method
    switch($role){
        case 'admin':
            echo 'You have full access.' ;
            break;
        case 'user':
            echo 'You can full access to data but cannot change structure';
            break;
        case 'operator':
            echo 'You can read only access';
        default:
            echo 'Your role is not recognized';
    }

    //Match method
    echo match($role){
        'admin' => 'You have full access.',
        'user' => 'You can full access to data but cannot change structure', 
        'operator' => 'You can read only access',
        default => 'Your role is not recognized',
    };    

    //Arrays
    $ar4 = [
            "yellow" => "banana",
            "red" => "cherry",
            "green" => "apple,
    ];
    echo $ar4['green'];
    $ar4['red'] = 'CHERRY';
    echo "Number of elements: " . count($ar4);
    echo '<br>';

    // print_r() display for count
    print_r($ar4);
    echo '<br>';

    // asort
    asort($ar4);
    print_r($ar4);
    echo '<br>';

    // ksort
    ksort($ar4);
    print_r($ar4);
    echo '<br>';


    */
    ?>

    /*
    // Loops In PHP
    <?php
    $ar = [25, 36, 48, 79, 26];
    echo $ar[3];
    ?>
    <?= $ar[1] ?><br>
    <?= '<br>' ?>
    <?php
    print_r($ar);
    $ar4 = [//associative array
        'red' => 'Cherry',
        'green' => 'Apple',
        'yellow' => 'Mango',
    ];
    echo $ar4["green"];
    ?>

    <h1>Loop while</h1>
    <?php
    $i = 0;
    while ($i < count($ar)) {
        echo $ar[$i] . ' ';
        $i++;
    }
    ?>

    <br>
    <h1>Loop for</h1>
    <?php
    for ($i = 0; $i < count($ar); $i++) {
        echo $ar[$i] . ' ';
    }
    ?>

    <br>
    <h1>Loop foreach</h1>
    <?php
    foreach ($ar as $value) {
        echo "$value ";
    }
    ?>
    <h2>Second syntax</h2>
    <?php
    foreach ($ar as $index => $value) {
        echo "$index => $value<br>";
    }
    ?>

    <h2>associative array</h2>
    <?php
    foreach ($ar as $index => $value) {
        echo "$index => $value<br>";
    }
    ?>
    <h1>Sorts: sort, rsort, asort, arsort, ksort, krsort</h1>
    <?php
    sort($ar);
    print_r($ar);
    echo '<br>';
    rsort($ar);
    print_r($ar);
    echo '<br>';
    echo '<br>';
    print_r($ar4);
    asort($ar4);
    echo '<br>';
    print_r($ar4);
    echo '<br>';
    arsort($ar4);
    print_r($ar4);
    echo '<br>';
    ksort($ar4);
    print_r($ar4);
    echo '<br>';
    ksort($ar4);
    print_r($ar4);
    ?>

    //VALUES
    <h1>Values in $_GET</h1>
    <?php
    $name = $_GET['name'] ?? '';
    $country = $_GET['country'] ?? '';
    $city = $_GET['city'] ?? '';
    ?>
    <table>
        <tr>
            <th>Name is: </th>
            <td><?= $name ?></td>
        </tr>
        <tr>
            <th>Country: </th>
            <td><?= $country ?></td>
        </tr>
        <tr>
            <th>City: </th>
            <td><?= $city ?></td>
        </tr>
    </table>

</body>

</html>