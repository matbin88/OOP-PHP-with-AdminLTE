<?php

class User {

	private $_db,
			$_data,
			$_sessionName,
			$_cookieName,
			$_isLoggedIn;

	public function __construct($user = null) {
		$this->_db = DB::getInstance();

		$this->_sessionName = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');

		if(!$user) {
			if(Session::exists($this->_sessionName)) {
				$user = Session::get($this->_sessionName);
				//echo $user;
				if($this->find($user)) {
					$this->_isLoggedIn = true;
				} else {
					// process logout
				}
			}
		} else {
			$this->find($user);
		}
	}

	public function create($fields = array()) {
		if(!$this->_db->insert('users', $fields)) {
			throw new Exception('There was a problem creating this account.');
		}
	}

	public function update($fields = array(), $id = null) {

		if(!$id && $this->isLoggedIn()) {
			$id = $this->data()->id;
		}

		if(!$this->_db->update('users', $id, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}

	public function find($user = null) {
		if($user) {
			// if user had a numeric username this FAILS...
			$field = is_numeric($user) ? 'id' : 'email'; 
			//$field = 'email'; 
			$data = $this->_db->get('users', array($field, '=', $user));

			if($data->count()) {
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}

	public function login($mobile = null, $password = null, $remember = false) {
		
		// print_r($this->_data);

		// check if username has been defined 
		if(!$mobile && !$password && $this->exists()) {
			Session::put($this->_sessionName, $this->data()->id);
		}else {
			$user = $this->find($mobile);

			if($user) {
				if($this->data()->password === Hash::make($password, $this->data()->salt)) {
					Session::put($this->_sessionName, $this->data()->id);

					if($remember) {
						$hash = Hash::unique();
						$hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));

						if(!$hashCheck->count()) {
							$this->_db->insert('users_session', array(
								'user_id' => $this->data()->id,
								'hash' => $hash
							));
						} else {
							$hash = $hashCheck->first()->hash;
						}

						Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
					}
					//save log
					/* $this->_db->insert('user_log', array(
						'user_id' => $this->data()->id,
						'logged_in_time' => date("Y-m-d H:i:s")
					)); */
					$this->saveLog(array(
						'user_id' => $this->data()->id,
						'logged_in_time' => date("Y-m-d H:i:s")
					));
					return true;
				}
			}
		}

		return false;
	}

	public function saveLog($fields) {
		$this->_db->insert('user_log', $fields);
	}

	public function hasPermission($key) {
		$group = $this->_db->get('groups', array('id', '=', $this->data()->group));
		if($group->count()) {
			$permissions = json_decode($group->first()->permissions, true);
			if($permissions[$key] == true) {
				return true;
			}
		}
		return false;
	}

	public function exists() {
		return (!empty($this->_data)) ? true : false;
	}

	public function logout() {

		$this->_db->delete('user_session', array('user_id', '=', $this->data()->id));

		Session::delete($this->_sessionName);
		Cookie::delete($this->_cookieName);
	}

	public function data() {
		return $this->_data;
	}

	public function isLoggedIn() {
		return $this->_isLoggedIn;
	}	

	public function getUserImage() {
		$userImage = "../uploads/user.png";
		if($this->data()) {			
			if(trim($this->data()->photo) != "")
			{
				$joined = $this->data()->joined;
				$ext = end(explode(".",$this->data()->photo));
				$userImage = "../uploads/".strtotime($joined).".".$ext;
			}			
		}
		return $userImage;
	}

	public function getAllUsers() {
		$users = $this->_db->get('users', array('id','>=','1'));
		//var_dump($users->results());
		return $users->results();
	}

	public function getLogData() {
		$users = $this->_db->get('user_log', array('user_id','=',$this->data()->id));
		//var_dump($users->results());
		return $users->results();
	}
}