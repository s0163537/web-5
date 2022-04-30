<?php
/**
 * Реализовать проверку заполнения обязательных полей формы в предыдущей
 * с использованием Cookies, а также заполнение формы по умолчанию ранее
 * введенными значениями.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    // Если есть параметр save, то выводим сообщение пользователю.
    $messages[] = 'Данные сохранены.';
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  $errors['name'] = !empty($_COOKIE['name_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['date'] = !empty($_COOKIE['date_error']);
  $errors['gender'] = !empty($_COOKIE['gender_error']);
  $errors['limbs'] = !empty($_COOKIE['limbs_error']);
  $errors['checkbox'] = !empty($_COOKIE['checkbox_error']);

  // Выдаем сообщения об ошибках.
  if ($errors['name']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('name_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div>Введите имя!</div>';
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div>Некорректный email!</div>';
  }
  if ($errors['date']) {
    setcookie('date_error', '', 100000);
    $messages[] = '<div>Выберите дату!</div>';
  }
  if ($errors['gender']) {
    setcookie('gender_error', '', 100000);
    $messages[] = '<div>Выберите пол!</div>';
  }
  if ($errors['limbs']) {
    setcookie('limbs_error', '', 100000);
    $messages[] = '<div>Выберите количество конечностей!</div>';
  }
  if ($errors['checkbox']) {
    setcookie('checkbox_error', '', 100000);
    $messages[] = '<div>Поставьте галочку!</div>';
  }

  // Складываем предыдущие значения полей в массив, если есть.
  $values = array();
  $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['date'] = empty($_COOKIE['date_value']) ? '' : $_COOKIE['date_value'];
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
  $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : $_COOKIE['limbs_value'];
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
  $values['checkbox'] = empty($_COOKIE['checkbox_value']) ? '' : $_COOKIE['checkbox_value'];
  if(empty($_COOKIE['ability_value']))
    $values['ability'] = array();
  else
    $values['ability'] = json_decode($_COOKIE['ability_value'], true);


  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $errors = FALSE;
  if (empty(htmlentities($_POST['name']))) {
    // Выдаем куку на день с флажком об ошибке в поле name.
    setcookie('name_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на год.
    setcookie('name_value', $_POST['name'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('email_value', $_POST['email'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (empty($_POST['date'])) {
    setcookie('date_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('date_value', $_POST['date'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (empty($_POST['gender'])) {
    setcookie('gender_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('gender_value', $_POST['gender'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (empty($_POST['limbs'])) {
    setcookie('limbs_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('limbs_value', $_POST['limbs'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (empty($_POST['checkbox'])) {
    setcookie('checkbox_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('checkbox_value', $_POST['checkbox'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if(!empty($_POST['bio'])){
    setcookie ('bio_value', $_POST['bio'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if(!empty($_POST['ability'])){
    $json = json_encode($_POST['ability']);
    setcookie ('ability_value', $json, time() + 12 * 30 * 24 * 60 * 60);
  }


  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('name_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('date_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('limbs_error', '', 100000);
    setcookie('checkbox_error', '', 100000);
  }

  // Сохранение в базу данных.
  $user = 'u47496';
  $pass = '3730253';
  $db = new PDO('mysql:host=localhost;dbname=u47496', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

  // Подготовленный запрос. Не именованные метки.
  try {
    // Запись в таблицу people
    $stmt = $db->prepare("INSERT INTO people SET name = ?, email = ?, date = ?, gender = ?, limbs = ?, bio = ?");
    $stmt -> execute([$_POST['name'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['limbs'], $_POST['bio']]);

    // Узнаём количество записей в таблице people
    $res = $db->query("SELECT max(id) FROM people");
    $row = $res->fetch();
    $count = (int) $row[0];

    $ability = $_POST['ability'];

    foreach($ability as $item) {
      // Запись в таблицу superability
      $stmt = $db->prepare("INSERT INTO superability SET people_id = ?, superability = ?");
      $stmt -> execute([$count, $item]);
    }
  }
  catch(PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: index.php');
}
