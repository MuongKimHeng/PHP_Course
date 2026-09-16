<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .box {
            aspect-ratio: 1;
            width: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            display: flex;
            gap: 8px;
        }
    </style>
</head>

<body>
    Write a PHP script that contain an array with the names of colors
    (more than four different colors).
    Use PHP to display four div elements of the same size on the page.
    At page load, all four divs must be filled with random, non-repeating colors selected from the array.
    Hint: look at the shuffle() or array_rand() functions.
    <?php
    $colors = [
        'red',
        'green',
        'blue',
        'yellow',
        'cyan',
        'meganta',
        'orange',
        'gray',
        'black',
        'white'
    ];
    
    // 1.Use shuffle()
    shuffle($colors);
    ?>

    <div class="container">
        <?php for ($i = 0; $i < 4; $i++) {
            ?>
            <div class="box" style="background-color" : <?= $colors[$i] ?>;">
            </div>
            <?php
        } ?>
    </div>

    //2. Use array_rand()
    $fourcolors = array_rand($colors, 4);
    ?>
    <div class="container">
        <?php foreach ($fourcolors as $i) {
            ?>
            <div class="box" style="background-color: <?= $colors[$i] ?>;">
                <?= $colors[$i] ?>
            </div>
            <?php
        } ?>
    </div>

</body>

</html>