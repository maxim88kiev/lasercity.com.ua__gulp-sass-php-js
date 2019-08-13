<?php

define( '_JEXEC', 1 );

if ( file_exists( __DIR__ . '/defines.php' ) ) {
    include_once __DIR__ . '/defines.php';
}
if ( !defined( '_JDEFINES' ) ) {
    //уровень вложенности нашего файла ajaxSending.php
    define( 'JPATH_BASE', __DIR__ . '/../../../' );
    require_once JPATH_BASE . '/includes/defines.php';
}
require_once JPATH_BASE . '/includes/framework.php';
//Тут мы уже можем использовать API Joomla Framework

if(isset($_POST['getOrganization'])) {
    $db = JFactory::getDbo();
    $query = "SELECT o.id,t.value FROM `#__lasercity_organizations` as o 
              LEFT JOIN `#__lasercity_translations_".$_GET['lan_code']."` as t ON t.object_id = o.id 
              WHERE t.object_column='title' AND t.object_name='organization' AND o.published='1' ";

    $db->setQuery($query);
    $result = $db->loadObjectList();

    $json['data'] = '<option value="0">Выберите организацию</option>';

    foreach ($result as $row) {
        $json['data'] .= '<option value="' . $row->id . '">' . $row->value . '</option>';
    }

    header('Content-Type', 'application/json; charset=utf-8');
    echo json_encode($json);
}
else if(isset($_POST['registration'])) {

    $json = null;
    $db = JFactory::getDbo();

    if (utf8_strlen($_POST['name']) < 3) {
        $json['error']['name'] = 'ФИО должно быть больше 3 символов';
    }

    $_GET['lan_code'] = stripcslashes(strip_tags($_GET['lan_code']));
    $_POST['new_organization'] = stripcslashes(strip_tags($_POST['new_organization']));
    $_POST['email'] = stripcslashes(strip_tags($_POST['email']));

    //узнаем ссылку на язык
    $db->setQuery("SELECT sef FROM `#__languages`  
                      WHERE lang_code = '".$_GET['lan_code']."' ");
    $result = $db->loadObjectList();

    foreach ($result as $row) {
        if (!empty($row->sef)) {
            $_GET['language'] = $row->sef;
            break;
        }
    }

    if($_POST['register_for']=='organization') {
        if (empty($_POST['organization']) && utf8_strlen($_POST['new_organization']) < 3) {
            $json['error']['new_organization'] = 'Название организации должно быть больше 3 символов';
        }

        $query = "SELECT t.value FROM `#__lasercity_organizations` as o 
              LEFT JOIN `#__lasercity_translations_" . $_GET['lan_code'] . "` as t ON t.object_id = o.id 
              WHERE t.object_column='title' AND t.value='" . $_POST['new_organization'] . "' ";

        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result as $row) {
            if (!empty($row->value)) {
                $json['error']['new_organization'] = 'Организация с таким именем уже существует, пожалуста введите другое название';
                break;
            }
        }

        if (utf8_strlen($_POST['phone']) != 19) {
            $json['error']['phone'] = 'Тефон введен не верно';
        }
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $json['error']['email'] = 'Введите правильный email';
    }

    $query = "SELECT email FROM `#__users`  
              WHERE email='".$_POST['email']."' AND activation='1' ";

    $db->setQuery($query);
    $result = $db->loadObjectList();
    foreach ($result as $row) {
        if(!empty($row->email)){
            $json['error']['email'] = 'Пользователь с таким email уже зарегистрирован, пожалуйста выполните вход';
            break;
        }
    }

    if (utf8_strlen($_POST['password']) < 6) {
        $json['error']['password'] = 'Пароль должен быть не меньше 6 символов';
    }
    if (utf8_strlen($_POST['repeat_password']) < 6) {
        $json['error']['repeat_password'] = 'Повтор пароля должен быть не меньше 6 символов';
    }
    if ($_POST['password'] != $_POST['repeat_password']) {
        $json['error']['password'] = 'Пароли не совпадают';
        $json['error']['repeat_password'] = 'Пароли не совпадают';
    }

    if(empty($json['error'])){
        $json['success'] = null;

        $_POST['password'] = JApplicationHelper::getHash($_POST['password']);
        $_POST['name'] = stripcslashes(strip_tags($_POST['name']));
        $_POST['register_for'] = stripcslashes(strip_tags($_POST['register_for']));
        $_POST['organization'] = stripcslashes(strip_tags($_POST['organization']));

        $username = explode("@",$_POST['email'])[0];

        //если введена новая организация
        if (empty($_POST['organization']) && !empty($_POST['new_organization'])){

            //добавлям новую запись оранизации - не опубликованная
            $query = "INSERT INTO `#__lasercity_organizations` SET 
                  published='0' ";

            $db->setQuery($query);
            $db->query();
            $_POST['organization'] = $db->insertid();

            //добавляем название огранизации в таблицу языков
            $query = "SELECT lang_code FROM `#__languages`  
                      WHERE lang_id > 0 ";

            $db->setQuery($query);
            $result = $db->loadObjectList();

            foreach ($result as $row) {
                if(!empty($row->lang_code)){
                    $db->setQuery("INSERT INTO `#__lasercity_translations_" . $row->lang_code . "` SET 
                                  object_name='organization',
                                  object_column='title',
                                  value = '".$_POST['new_organization']."', 
                                  object_id = '" . $_POST['organization'] . "'
                                  ");
                    $db->query();
                }
            }

        }

        //добавляем пользователя и данные организации в базу - пользователь пока еще не подтвердил регистрацию по почте activation = '0'
        $query = "INSERT INTO `#__users` SET 
                  email='" . $_POST['email'] . "',
                  password='" . $_POST['password'] . "',
                  username = '$username', 
                  name = '" . $_POST['name'] . "', 
                  activation = '0', 
                  registerDate = NOW() ";

        $db->setQuery($query);
        $db->query();
        $last_user_id = $db->insertid();

        $query = "INSERT INTO `#__users_description` SET 
                  tip_user='" . $_POST['register_for'] . "',
                  phone = '" . $_POST['phone'] . "', 
                  organization_id = '" . (int)$_POST['organization'] . "', 
                  user_id = '" . (int)$last_user_id . "' ";

        $db->setQuery($query);
        $db->query();

        //отправляем письмо с подтверждением регистрации
        $reg_code=mt_rand(1000,9000);
        $pr_reg="https://".$_SERVER['SERVER_NAME']."/templates/lasercity/ajax/ajaxSending.php?language=".$_GET['language']."&regcode=".$reg_code."&post=".$last_user_id;

        $message="
			<!DOCTYPE html>
			<head>
			<meta charset='utf-8'>
			<title>Регистрация на ".$_SERVER['SERVER_NAME']."</title>
			</head>
				<body>
					<div title='".$_SERVER['SERVER_NAME']."' style='background:#F0F0F0;width:700px;height:auto;margin:auto;padding:20px 50px 40px 50px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;'>
					<a href='https://".$_SERVER['SERVER_NAME']."/".$_GET['language']."/'>".strtoupper($_SERVER['SERVER_NAME'])."<!--<img src='https://".$_SERVER['SERVER_NAME']."/img/logo.svg' alt=''>--></a>
						<div style='text-align:justify;margin-top:20px;padding:20px;background:white;font-size:15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;-moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);'>
							<h3>Здравствуйте!</h3>
							Чтобы подтвердить регистрацию на ".$_SERVER['SERVER_NAME'].", нажмите на кнопку «Зарегистрироваться».
							<a href='".$pr_reg."' style='text-decoration:none;'><div style='text-align:center;margin:auto;padding:10px;font-size:16px;font-weight:bold;color:white;background:#06c;margin-top:20px;white-space:nowrap;width:170px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;'>Зарегистрироваться</div></a>
						</div>
						<div style='text-align:justify;margin-top:20px;padding:20px;background:white;font-size:15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;-moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);'>
							<h3>Важно знать</h3>
							Регистрация позволит Вам убрать рекламу с сайта, а также пользоваться всеми привелегиями сайта.
						</div>							
						<div style='font-size:12px;margin-top:20px;width:100%;text-align:center;color:#989898;'>Пожалуйста, не отвечайте на это сообщение оно было отправлено автоматически.</div>
					</div>
				</body>
			</html>";
        if (mail($_POST['email'], "Регистрация на ".$_SERVER['SERVER_NAME'], $message, "From: ".$_SERVER['SERVER_NAME']." <noreply@".$_SERVER['SERVER_NAME'].">\r\n" ."MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8")){
            $json['success'] = 'На ваш e-mail '.explode("@",$_POST['email'])[0].'<a href="https://'.explode("@",$_POST['email'])[1].'">'.explode("@",$_POST['email'])[1].'</a> было отправлено письмо с подтверждение регистрации, пожалуйста подтвердите регистрацию!';
        }

    }

    header('Content-Type', 'application/json; charset=utf-8');
    echo json_encode($json);
}
//подтверждение регистрации с почты
else if(isset($_GET['regcode'])) {
    $_GET['language'] = stripcslashes(strip_tags($_GET['language']));
    $_GET['regcode'] = stripcslashes(strip_tags($_GET['regcode']));
    $_GET['post'] = stripcslashes(strip_tags($_GET['post']));

    if(!empty($_GET['language']) && !empty($_GET['regcode']) && !empty($_GET['post'])) {
        $db = JFactory::getDbo();

        $db->setQuery("UPDATE `#__users` SET activation = '1' WHERE id='".(int)$_GET['post']."'");
        $db->query();

        exit('Подтверждение регистрации, успешно!');
    }
    else{
        exit('Ошибка подтверждения регистрации, пожалуйста попробуйте пожже!');
    }
}