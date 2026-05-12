<?php 
if (!is_dir('../../uploads')) {
    mkdir('../../uploads', 0777, true);
}
if ($_FILES['image']) {
    $file = $_FILES['image'];
    $allowedtypes = ['image/png', 'image/jpeg'];

    if (in_array($file['type'], $allowedtypes)) {
        $filename = uniqid();
        $uploadpath = '../../uploads' . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadpath)) {
            list($width, $height) = getimagesize($uploadpath);

            $info = "Файл: $filename\nШирина: $width px\nВысота: $height px\n\n";
            file_put_contents('../../image_info.txt', $info, FILE_APPEND);

            echo "<h1>Изображение загружено!</h1>";
            echo "<p><strong>Размеры:</strong></p>";
            echo "<ul>
                    <li>Ширина: $width px</li>
                    <li>Ширина: $height px</li>
                </ul>";
            echo "<img src='$uploadpath' alt='upload image'>";
        }
        else {
            echo "Ошибка загрузки файла";
        }
    }
    else {
        echo "Ошибка недопустимый формат";
    }
}else {
        echo "Файл не был загружен";       
    }
?>