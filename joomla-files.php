<?php
----- Редирект с сообщением -----
$app = JFactory::getApplication();
$app->enqueueMessage('Ви успішно увійшли на сайт');
$app->redirect($red);


---- Доступ\права -----
$user =& JFactory::getUser();
$user->guest;
$user->groups[8];


---- Авторизация ------
$credentials = array( 'username'=>$user['uid'], 'password'=>'111111' );
$options = array( 'remember'=>true );
JFactory::getApplication()->login( $credentials, $options )


---- Робота с БД в joomla ----
$db =& JFactory::getDBO();
$q = "SELECT * FROM " . self::$table_game ." WHERE secret_key = '" . $secret_key ."' ORDER BY `id` DESC";
$db->setQuery($q);
return $db->loadObjectList();


---- AJAX запрос ----

url: '/go/index.php?option=com_ajax&module=ajaxgame&format=json&var1'


---- Редактор -----

$editor = JFactory::getEditor();
echo $editor->display( 'about',  $model->about, '100%', '', '30', '10', false);


---- Пароль ------
	
use Joomla\CMS\Crypt\Crypt;
use Joomla\CMS\Crypt\CryptPassword;
use Joomla\CMS\Crypt\Password\SimpleCryptPassword;


$crypt = new SimpleCryptPassword();
$password = $crypt->create($_POST['password']);

				
