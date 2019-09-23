<?php
/**
 * Загружает полученную картинку на сервер и возвращает ссылку на неё
 */
// проверим, что в запросе действительно изображение
if(isset($_FILES['image'])){
    $img = $_FILES['image'];       
    // путь для сохранения файла (добавляем rand, чтобы не допустить перезаписи файла другим с таким же именем)
    $path = "images/" . rand().$img["name"];
    // записываем полученное изображение по указанному пути
    move_uploaded_file($img['tmp_name'],$path);
    // вычисление размера полученного изображения
    $data = getimagesize($path);
    //прямая ссылка на изображение    
    // $link = "http://$_SERVER[HTTP_HOST]"."/km/".$path;
    $link = "/km/$path";
    // Формирование и отправка ответа в виде JSON
    $res = array("data" => array( "link" => $link, "width" => $data[0], "height" => $data[1]));
    echo json_encode($res);
}
?>
