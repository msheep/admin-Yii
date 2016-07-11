<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class LisController extends Controller
{
	/**
	 *  后台认证
	 * @param  $menu 是否请求菜单
	 * @throws CHttpException
	 * @return  <multitype:, mixed, CActiveRecord>  菜单列表 ; boolean 认证: true | false
	 * 2013-5-4
	 */
	public function auth($menu = NULL, $allFalg = false){
	
		// 获得用户角色列表 11,12
		$uid = Yii::app()->user->id;
		if(!$uid){
			$this->redirect("/daigou/main/login");
			Yii::app()->end();
		}
		$adminRole = AdminRole::model()->find("adminId=$uid");
		if(!$adminRole) throw new CHttpException(200, '该用户需要分配角色');
	
		// 获得角色菜单列表 2,3,4
		$criteria = new CDbCriteria;
        if(!$allFalg){
            $criteria->condition = "roleID in ({$adminRole->roleIDs})";
        }
		$roleMenus = RoleMenu::model()->findAll($criteria);
		if(!$roleMenus) throw new CHttpException(200, "该角色{$adminRole->roleIDs}需要分配菜单");
	
		$menuIDsArray = array();
		foreach ($roleMenus as $key => $roleMenu) {
			$menuIDsArray = array_merge($menuIDsArray, explode(',', $roleMenu->menuIDs));
		}
		$menuIDsArray = array_flip(array_flip($menuIDsArray));
		$menuIDs = implode(',',  $menuIDsArray);
		//PTrack($menuIDsArray);
		
		// 获得菜单
        if(!$allFalg){
            $criteria->condition = "id in ({$menuIDs}) and isEnable=0";
        }
		$criteria->order = "sort asc";
		$menus = Menu::model()->findAll($criteria);
		if(!$menus) throw new CHttpException(200, "无效菜单");
		// 返回菜单列表
		if($menu) return $menus;
		if($this->isTopAdmin()) return true;
		return true;
		// 验证Url 匹配 并且 菜单可用状态
		foreach ($menus as $key => $value) {
			//  /daigou/platform/admin 包含 /daigou/platform/admin.* 的权限  
			if($value ->url && $value ->isEnable==='0' && strpos($_SERVER["REQUEST_URI"], $value ->url) === 0)
			return true;
		}
		return false;
	}
	
public function init(){
		parent::init();
		foreach ($_POST as $key => $value){
			if(!isset($_GET[$key])) $_GET[$key]=$_POST[$key];
		}
		if(!strstr(Yii::app()->request->getUrl(), 'auto') ===TRUE && !strstr(Yii::app()->request->getUrl(), 'AutoStatus') 
                && !strstr(Yii::app()->request->getUrl(), 'qunarSuccessCount')){
			if(!((defined("TEST") || defined("LOCALHOST")) && strstr(Yii::app()->request->getUrl(), 'orderdetail') !==false)){
			if(Yii::app()->user->id){
				$this->auth();
			}else if (!Yii::app()->user->id && strpos(Yii::app()->request->url, 'login') === FALSE) {
				$this->redirect('/daigou/main/login');
			}
			}
		}
	}
	
	public function getXingQi($date){
		if($date==1){
			return  "一";
		}else if($date==2){
			return "二";
		}else if($date==3){
			return  "三";
		}else if($date==4){
			return  "四";
		}else if($date==5){
			return  "五";
		}else if($date==6){
			return "六";
		}else if($date==0){
			return "日";
		}else{
			return "";
		}
	}
	
}