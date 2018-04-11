<?php

defined('_JEXEC') or die ;


require_once __DIR__ .'/helper.php';

$model = new modIcoFormHelper();
$model->active = 1;
if(isset($model->user->groups[8]) && $model->user->groups[8] === '8'){

	if(isset($_GET['remove'])){
		if($model->remove($_GET['remove'])){
			$app->redirect('/publish-ico?done=deleted');
		}
	}

	if(isset($_GET['publish'])){
		if($model->publish($_GET['publish'])){
			$app->redirect('/publish-ico?done=publish');
		}
	}

	if(isset($_GET['edit'])){
		$model->updateModel($_GET['edit']);
		if(isset($_POST) && !empty($_POST)){
			if($model->load() && $model->validate()){
				if($model->update($_GET['edit'])){
					if(isset($_POST['social']) && !empty($_POST['social'])){
						$model->removeSocial($_GET['edit']);
						$model->saveSocial();
					}
					if(isset($_POST['star'])){
						$model->removeStar($_GET['edit']);
						$model->saveStar();
					}

					$app->redirect('/publish-ico?done=update');
				}
			}
		}
	}
}

if(isset($_POST) && !empty($_POST) && !isset($_GET['edit']) && !isset($_GET['delete'])){

	if($model->load() && $model->validate()){

		if($model->save()){
			if(isset($_POST['social']) && !empty($_POST['social'])){
				$model->saveSocial();
			}
			if(isset($_POST['star'])){
				$model->saveStar();
			}
			$app = JFactory::getApplication();
			$app->enqueueMessage('Created');
			$app->redirect('/publish-ico?done=created');
		}
		

	}
}


$socials = $model->getSocials();
$categories = $model->getCategories();

require JModuleHelper::getLayoutPath('mod_ico_form', $params->get('layout', 'default'));