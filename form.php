<?php
if (isset($_POST['name'])) {$name = $_POST['name'];}
if (isset($_POST['email'])) {$email = $_POST['email'];}
if (isset($_POST['phone'])) {$address = $_POST['phone'];}
if (isset($_POST['message'])) {$message = $_POST['message'];}
$to = "newlife@ananapa.ru";
$headers = "Content-type: text/plain; charset = utf8";
$subject = "Сообщение от $name с сайта ANANAPA";
$message = "Имя: $name \nЭлектронный адрес: $email \nТелефон: $phone \nСообщение: $message";
$send = mail($to, $subject, $message, $headers);
if ($send = 'true')
{
header('Refresh: 3; URL=index.html');
}
else
{
echo "Error!";
}
?>