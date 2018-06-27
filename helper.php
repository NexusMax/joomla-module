<?
defined('_JEXEC') or die('Доступ запрещен');

class modIcoFormHelper
{
	 /**
        globals settings
    **/

    protected $tableUser               = '#__users';
    protected $tableIco                = '#__ico';
    protected $tableIcoTeam            = '#__ico_team';
    protected $tableIcoRoadMap         = '#__ico_road_map';
    protected $tableIcoCategories      = '#__ico_categories';
    protected $tableIcoHasCategory     = '#__ico_has_category';
    protected $tableIcoSocial          = '#__ico_social';
    protected $tableIcoHasSocial       = '#__ico_has_social';
    protected $tableIcoStar            = '#__ico_star';
    protected $config;
    protected $db;

    public $user;
    public $image_dir;
    public $image_path;
    public $errors_message = [];

    public $category_id = [];


    public function __construct()
    {
        // $this->config = new JConfig();
        $this->db =JFactory::getDBO();
        $this->setProperties();

        
        $this->user =JFactory::getUser();
        $this->image_dir = JURI::base() . 'images/ico/';
        $this->image_path = dirname(dirname(__DIR__)) . '/images/ico/';
    }

    private function setProperties()
    {
        $q = "SHOW COLUMNS FROM " . $this->tableIco;
        $this->db->setQuery($q);
    
        foreach ($this->db->loadAssocList() as $key) {
            $this->$key['Field'] = null;
        }

        $q = "SELECT * FROM " . $this->tableIcoSocial;
        $this->db->setQuery($q);

        foreach ($this->db->loadAssocList() as $key) {
            $this->social[$key['form_name']] = null;
        }
        $this->star = null;

        $q = "SHOW COLUMNS FROM " . $this->tableIcoTeam;
        $this->db->setQuery($q);
        
        $team_field = [];
        foreach ($this->db->loadAssocList() as $key) {
            $team_field[$key['Field']] = null;
        }
        $this->team[] = $team_field;

        $q = "SHOW COLUMNS FROM " . $this->tableIcoRoadMap;
        $this->db->setQuery($q);

        $road_map_field = [];
        foreach ($this->db->loadAssocList() as $key) {
            $road_map_field[$key['Field']] = null;
        }
        $this->road_map[] = $road_map_field;

        // echo '<pre>';
        // print_r($this);
        // die;

    }
    private function magic($name, $value)
    {
        $this->$name = $value;
    }
 
    public function __set($name, $value)
    {
        $this->magic($name, $value);
    }

    public function load()
    {    
    	$old_team = $this->team;

        foreach ($_POST as $key => $value) {
            $this->$key = $value;
        }

        foreach ($this->team as $key => $value) {
        	if(!isset($this->team[$key]['photo']) && isset($old_team[$key]['photo'])){
        		$this->team[$key]['photo'] = $old_team[$key]['photo'];
        	}
        }

        if($this->user_id === null){
            $this->user_id = $this->user->id;
        }
        $this->alias = $this->str2url($this->name);

        return true;
    }


    public function save()
    {
        $attrVal = $this->getAttrVal();
        $attr = $attrVal['attr'];
        $val = $attrVal['val'];


        $q = "INSERT INTO " . $this->tableIco . " (logo,  user_id, alias, $attr) VALUES('$this->logo',  '$this->user_id', '$this->alias', $val)";

    
        $this->db->setQuery($q);
        $this->db->execute();

        $this->saveCategories();
        $this->saveTeam();
        $this->saveRoadMap();

        return true;
    }

    public function getAttrVal()
    {
        $i = 0;
        $attr = '';
        $val = '';

        
        if(isset($this->user->groups[8]) && $this->user->groups[8] === '8'){
        	if(!isset($_POST['active'])){
        		$_POST['active'] = 0;
        		$this->active = 0;
        	}
        	if(!isset($_POST['premium'])){
        		$_POST['premium'] = 0;
        		$this->premium = 0;
        	}
        }

        $checkbox = [1 => 'bonus', 2 => 'whitelist_kyc', 3 =>  'bounty'];
        foreach ($checkbox as $key => $value) {
        	if(!isset($_POST[$value])){
				$_POST[$value] = 0;
	        	$this->$value = 0;
			}
        }

        foreach ($this->team as $key => $vall) {

            if(!isset($vall['advisor'])){
                $this->team[$key]['advisor'] = 0;
            }
            if(isset($vall['advisor']) && $vall['advisor'] === 'on'){
                $this->team[$key]['advisor'] = 1;
            }
            if(!isset($vall['member'])){
                $this->team[$key]['member'] = 0;
            }
            if(isset($vall['member']) && $vall['member'] === 'on'){
                $this->team[$key]['member'] = 1;
            }
        }

        foreach ($this->road_map as $key => $vall) {
        	$this->road_map[$key]['created_at'] = date('Y-m-d H:i:s', strtotime($this->road_map[$key]['created_at']));;
        }

        $this->ico_start = date('Y-m-d H:i:s', strtotime($_POST['ico_start']));
        $this->ico_end = date('Y-m-d H:i:s', strtotime($_POST['ico_end']));

        foreach ($_POST as $key => $value) {
            ++$i;
            if($key !== 'social' && $key !== 'star' && $key !== 'category_id' && $key !== 'team' && $key !== 'road_map'){
                $attr .= "$key";
                if($this->$key === 'on'){
                    $val .= "'1'";
                }else{
                    $val .= "'" . str_replace("'"," ",$this->$key)  . "'";
                }

                if($i !== count($_POST)){
                    $attr .= ', ';
                    $val .= ', ';
                }
            }


        }
     	


        return [
            'attr' => $attr,
            'val' => $val,
        ];
    }

    public function saveTeam()
    {
        $ico = $this->getIco();

        foreach ($this->team as $key => $value) {
            if(!empty($value)){

                $q = "INSERT INTO " . $this->tableIcoTeam . " (`ico_id`, `name`, `title`, `linkedin`, `advisor`, `member`, `photo`) VALUES('" . $ico->id . "', '" . $value['name'] . "', '" . $value['title'] . "', '" . $value['linkedin'] . "', '" . $value['advisor'] . "', '" . $value['member'] . "', '" . $value['photo'] . "')";


                $this->db->setQuery($q);
                $this->db->execute();
            }

        }

        return true;
    }

    public function saveRoadMap()
    {
        $ico = $this->getIco();

        foreach ($this->road_map as $key => $value) {
            if(!empty($value)){

                $q = "INSERT INTO " . $this->tableIcoRoadMap . " (`ico_id`, `created_at`, `desc`) VALUES('" . $ico->id . "', '" . $value['created_at'] . "', '" . $value['desc'] . "')";

                $this->db->setQuery($q);
                $this->db->execute();
            }

        }

        return true;
    }

    public function getIcoTeam($ico_id)
    {
    	$q = "SELECT * FROM " . $this->tableIcoTeam . " WHERE `ico_id` = '" . $ico_id . "'";
        $this->db->setQuery($q);
    
        return $this->db->loadObjectList();
    }

    public function getIcoTeamArray($ico_id)
    {
    	$q = "SELECT * FROM " . $this->tableIcoTeam . " WHERE `ico_id` = '" . $ico_id . "'";
        $this->db->setQuery($q);
    
        return $this->db->loadAssocList();
    }

    public function getIcoRoadMap($ico_id)
    {
    	$q = "SELECT * FROM " . $this->tableIcoRoadMap . " WHERE `ico_id` = '" . $ico_id . "'";
        $this->db->setQuery($q);
    
        return $this->db->loadAssocList();
    }

    public function removeTeam($ico_id)
    {
    	$team = $this->getIcoTeam($ico_id);

    	if(!empty($team)){
    		foreach ($team as $key) {
    			if(!empty($key->photo)){
			        @unlink($this->image_path . $key->photo);
    			}
    		}
    	}
        $q = "DELETE FROM " . $this->tableIcoTeam . " WHERE `ico_id` = '" . $ico_id . "'";
        $this->db->setQuery($q);
        $this->db->execute();

        return true;
    }

    public function removeCategory($ico_id)
    {
        $q = "DELETE FROM " . $this->tableIcoHasCategory . " WHERE `ico_id` = '" . $ico_id . "'";
        $this->db->setQuery($q);
        $this->db->execute();

        return true;
    }

    public function removeRoadMap($ico_id)
    {
        $q = "DELETE FROM " . $this->tableIcoRoadMap . " WHERE `ico_id` = '" . $ico_id . "'";
        $this->db->setQuery($q);
        $this->db->execute();

        return true;
    }

    public function saveSocial()
    {
        $ico = $this->getIco();

        foreach ($_POST['social'] as $key => $value) {
            if(!empty($value)){
                $social = $this->getSocial($key);

                $q = "INSERT INTO " . $this->tableIcoHasSocial . " (`ico_id`, `social_id`, `value`) VALUES('$ico->id', '$social->id', '" . str_replace("'"," ",$value)  . "')";
            
                $this->db->setQuery($q);
                $this->db->execute();
            }

        }

        return true;
    }

    public function publish($ico_id)
    {
        $q = "UPDATE " . $this->tableIco . " SET `active` = '1' WHERE `id` = '" . $ico_id . "'";
        $this->db->setQuery($q);
        $this->db->execute();

        return true;
    }

    public function deleteCategories($ico_id)
    {
        $q = "DELETE FROM " . $this->tableIcoHasCategory . " WHERE `ico_id` = '" . $ico_id . "'";
        $this->db->setQuery($q);
        $this->db->execute();
    }

    public function saveCategories()
    {
        $ico = $this->getIco();

        if(!empty($this->category_id)){
            foreach ($this->category_id as $key) {
                $q = "INSERT INTO " . $this->tableIcoHasCategory . " (`ico_id`, `category_id`) VALUES('$ico->id', '$key')";
                $this->db->setQuery($q);
                $this->db->execute();
            }
        }
    }

    public function validate()
    {
        $this->upload();
        return true;
    }

    public function rus2translit($string)
    {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    public function str2url($str)
    {
        // переводим в транслит
        $str = $this->rus2translit($str);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_а-яА-Я]+~u', '-', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");
        // $old_str = Ads::find()->where(['alias' => $str])->one();
        if(!empty($old_str))
            $str = $str . '_' . mb_substr(Yii::$app->security->generateRandomString(), 0, 8);

        if(empty($str))
            $str = '-' . time();
        return $str;
    }

    public function getSocial($form_name)
    {
        $q = "SELECT * FROM " . $this->tableIcoSocial . " WHERE `form_name` = '$form_name' LIMIT 1";
        $this->db->setQuery($q);
        
        return $this->db->loadObject();
    }

    public function getIco()
    {

        $q = "SELECT * FROM " . $this->tableIco . " WHERE `name` = '" . $this->name . "' 
        AND `alias` = '" . $this->alias . "' 
        AND `price` = '" . $this->price . "' 
        AND `token` = '" . $this->token . "' 
        AND `site` = '" . $this->site . "' 
        AND `min_investment` = '" . $this->min_investment . "' 
        AND `user_id` = '" . $this->user_id . "' LIMIT 1";
        $this->db->setQuery($q);

        return $this->db->loadObject();
    }

    public function getIcoById($id)
    {
        $q = "SELECT * FROM " . $this->tableIco . " WHERE `id` = '" . $id . "' LIMIT 1";
        $this->db->setQuery($q);

        return $this->db->loadObject();
    }

    public function getSocials()
    {
        $q = "SELECT * FROM " . $this->tableIcoSocial . " WHERE active = 1";
        $this->db->setQuery($q);
    
        return $this->db->loadObjectList();
    }

    public function getIcoSocial($ico_id)
    {
        $q = "SELECT * FROM " . $this->tableIcoHasSocial . " as a
        LEFT JOIN " . $this->tableIcoSocial . " as b ON a.social_id = b.id 
        WHERE `ico_id` = '" . $ico_id . "' GROUP BY `social_id`";
        $this->db->setQuery($q);
    
        return $this->db->loadObjectList();
    }

    public function getCategories()
    {
        $q = "SELECT * FROM " . $this->tableIcoCategories . " WHERE active = 1";
        $this->db->setQuery($q);
    
        return $this->db->loadObjectList();
    }

    public function getStarById($ico_id)
    {
    	$q = "SELECT * FROM " . $this->tableIcoStar . " WHERE `ico_id` = '" . $ico_id . "' LIMIT 1";
        $this->db->setQuery($q);
    
        return $this->db->loadObject();
    }

    public function saveStar()
    {
    	$ico = $this->getIco();
    	$value = $_POST['star'];

        $q = "INSERT INTO " . $this->tableIcoStar . " (`ico_id`, `value`, `created_at`) VALUES('$ico->id', '$value', '" . date('Y-m-d H:i:s', time())  . "')";
    
        $this->db->setQuery($q);
        $this->db->execute();
        

        return true;
    }

    public function remove($id)
    {

        $ico = $this->getIcoById($id);

        if(!empty($ico->logo)){
            @unlink($this->image_path . $ico->logo);
        }

        $q = "DELETE FROM " . $this->tableIco . " WHERE `id` = '" . $id . "'";
        $this->db->setQuery($q);
        $this->db->execute();

        if($this->removeStar($id) && $this->removeSocial($id) && $this->removeTeam($id) && $this->removeCategory($id) && $this->removeRoadMap($id)){
            return true;
        }
        return false;
    }

    public function update($id)
    {

        $attrVal = $this->getAttrVal();
        $attr = $attrVal['attr'];
        $val = $attrVal['val'];

        $attr = explode(',', str_replace("'", '', str_replace(' ', '', $attr)));
        $val = explode(',', str_replace("'", '', str_replace(' ', '', $val)));

        $q = "UPDATE " . $this->tableIco . " SET `logo` = '$this->logo', `user_id` = '$this->user_id', `alias` = '$this->alias', ";
        for($i = 0; $i < count($val); $i++) {
        	if($attr[$i] === 'ico_start' || $attr[$i] === 'ico_end' || $attr[$i] === 'road_map_date'){
        		$val[$i] = date('Y-m-d H:i:s', strtotime($val[$i]));
        	}
            $q .= " `" . $attr[$i] . "` = '" . $val[$i] . "'";
            if(count($val) - 1 !== $i){
                $q .= ',';
            }
        }
        $q .= " WHERE `id` = '" . $id . "'";


        $this->db->setQuery($q);
        $this->db->execute();

        $this->deleteCategories($id);
        $this->saveCategories();

        $this->removeTeam($id);
        $this->saveTeam();

        $this->removeRoadMap($id);
        $this->saveRoadMap();

        return true;
    }

    public function updateModel($id)
    {
        $ico = $this->getIcoById($id);
        foreach ($ico as $key => $value) {
            if($key === 'ico_start' || $key === 'ico_end' || $key === 'road_map_date'){
                $value = strftime('%Y-%m-%dT%H:%M:%S', strtotime($value));
            }
            $this->$key = $value;
        }
        $social = $this->getIcoSocial($ico->id);
 
        $star = $this->getStarById($ico->id);
        if(!empty($star)){
        	$this->star = $star->value;
        }

        foreach ($social as $key => $value) {
            $this->social[$value->form_name] = $value->value;
            // echo $key; echo '<br>';
        }

        $categories = $this->getIcoCategories($ico->id);

        if(!empty($categories)){
            foreach ($categories as $key) {
                $this->category_id[] = $key->category_id;
            }
          
        }

        $team = $this->getIcoTeamArray($ico->id);

        if(!empty($team)){
            $this->team = $team;
          
        }

        $road_map = $this->getIcoRoadMap($ico->id);

        if(!empty($road_map)){
            $this->road_map = $road_map;
          
        }

        // echo '<pre>';
        // print_r($this);
        // die;
    }


    public function getIcoCategories($ico_id)
    {
        $q = "SELECT * FROM " . $this->tableIcoHasCategory . " WHERE `ico_id` = '" . $ico_id . "'";
        $this->db->setQuery($q);
    
        return $this->db->loadObjectList();
    }

    public function removeStar($ico_id)
    {
        $q = "DELETE FROM " . $this->tableIcoStar . " WHERE `ico_id` = '" . $ico_id . "'";

        $this->db->setQuery($q);
        return $this->db->execute();
    }

    public function removeSocial($ico_id)
    {
        $q = "DELETE FROM " . $this->tableIcoHasSocial . " WHERE `ico_id` = '" . $ico_id . "'";

        $this->db->setQuery($q);
        return $this->db->execute();
    }

    protected function upload()
    {
    	
        if(!empty($_FILES['logo']['name'])){
            $load = false;
            $info = new SplFileInfo(basename($_FILES['logo']['name']));
            $name = mb_substr(md5(basename($_FILES['logo']['name'])), 0, 6) . '.' . $info->getExtension();
            if(is_string($this->logo) && $name !== $this->logo){
                $load = true;
                @unlink($this->image_path . $this->logo);
            }elseif(!is_string($this->logo)){
                $load = true;
            }
            $this->logo = $name;
            if($load){
                if(UPLOAD_ERR_OK == $_FILES['logo']['error']){
                    move_uploaded_file($_FILES['logo']['tmp_name'], $this->image_path . $this->logo);
                }
            }
        }
        
        if(!empty($_FILES['team']['name'][0]['photo'])){

            foreach ($this->team as $key => $value) {
		    	if(isset($value['photo'])){
		    		@unlink($this->image_path . $value['photo']);
		    	}
            }
            for($i = 0; $i < count($_FILES['team']['name']); $i++){

            	$info = new SplFileInfo(basename($_FILES['team']['name'][$i]['photo']));
                $name = mb_substr(md5(basename($_FILES['team']['name'][$i]['photo'])), 0, 4) . mb_substr(time(), 0, 3) . '.' . $info->getExtension();
               
                $this->team[$i]['photo'] = $name;
                
                if(UPLOAD_ERR_OK == $_FILES['team']['error'][$i]['photo']){
                    move_uploaded_file($_FILES['team']['tmp_name'][$i]['photo'], $this->image_path . $this->team[$i]['photo']);
                }

            }
        }
        // echo '<pre>';
        // print_r($this->team);
        // die;

        return true;
    }


    public function send()
    {
        $this->errors_message = [];

        // echo '<pre>';
        // print_r($_POST);
        // die;
        if(isset($_POST) && !empty($_POST)){
            if(!isset($_POST['flirt_item'])){
                $this->errors_message[] = 'You did not choose a picture!';
            }else{
                if(empty($_POST['flirt_item'])){
                    $this->errors_message[] = 'You did not choose a picture!';
                }
            }

            if(!isset($_GET['id'])){
                $this->errors_message[] = 'Girl not found!';
            }else{
                if(empty($_GET['id'])){
                    $this->errors_message[] = 'Girl not found!';
                }
            }
            // echo '<pre>';
            // print_r($this->errors_message);
            // die;

            // if(!isset($_POST['message']) || !empty($_POST['message'])){

            //  $errors = true; 
            // }

            if(!empty($this->errors_message)){
                return false;
            }else{
                $this->insert();
                $this->send = true;
            }
        }else{
            return false;
        }
 
    }


    public function sendMail()
    {
        $user = $this->getUserById();

        $to      = $user->email;
        $subject = 'Sweetmarriagegalaxy - Registration Confirmation';
        $message = 'Hi, ' . $user->name . '!' . "\r\n" . 'Click the link to verify the identity: <a href="https://sweetmarriagegalaxy.com/custom-success?hash=' . $user->otpKey . '">Link</a>';
        $headers = 'From: sweetmarriagegalaxy@mail.com' . "\r\n" .
            'Reply-To: sweetmarriagegalaxy@mail.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

    public function getAjax()
    {
        echo 'Это ajax запрос!';
    }
    public function inserobje(){
    
    $db = JFactory::getDbo();

		$row = new JObject();
		$row->email = $email;
		$row->name =  $name;
		$row->username = $name;
		$row->password = md5($password);
		$row->registerDate = date('Y-m-d H:i:s', time());
		$ret = $db->insertObject('#__users', $row);

		$row = new JObject();
		$row->user_id = $db->insertid();
		$row->group_id = 2;
		$ret = $db->insertObject('#__user_usergroup_map', $row);
    }
}
