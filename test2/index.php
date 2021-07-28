<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Types\Type_A;
use App\Types\Type_B;
use App\Validator;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;

$validator = new Validator(new Type_A);
try {
    $errors = $validator->validate()->getErrors();
} catch (IOException | UnsupportedTypeException | ReaderNotOpenedException $e) {
    var_dump(get_class($e));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avana Test</title>
</head>

<body>
<table border="1">
    <thead>
    <tr>
        <th>Row</th>
        <th>Error</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($errors as $index => $error) : ?>
        <tr>
            <td><?= $index ?></td>
            <td><?= $error['error'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>

</html>