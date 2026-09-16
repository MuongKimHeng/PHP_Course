<?php
$uploadsDir = 'uploads/';
$contactsFile = 'contacts.json';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_INT);
    $phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_NUMBER_INT);

    if ($name && $email && $phone && isset($_FILES['image'])) {

        //Ensure the uploads directory exitst
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $imagePath = $uploadsDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $contacts = file_exists($contactsFile) ? json_decode(file_get_contents($contactsFile)) : [];
            $contacts[] = [
                'id' => rand(100000000, 20000000),
                'name' => $name,
                "email" => $email,
                "phone" => $phone,
                "image" => $imagePath
            ];

            file_put_contents($contactsFile, json_encode($contacts, JSON_PRETTY_PRINT));

            echo "Contact added: $name ($email, $phone)";
        } else {
            echo "Image upload failed";
        }
    } else {
        echo "Invalid input!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

<form action="" method="POST" enctype="multipart/form-data">
    <label>Name:</label>
    <input type="text" name="name">

    <label>Email:</label>
    <input type="email" name="email">

    <label>Phone:</label>
    <input type="text" name="phone">

    <label>Contact Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit">Add Contact</button>
</form>

</body>

</html>