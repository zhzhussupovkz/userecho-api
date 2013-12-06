<?php

/**
* UserEchoAPI class
* 
* @author zhzhussupovkz@gmail.com
* 
* The MIT License (MIT)
*
* Copyright (c) 2013 Zhussupov Zhassulan zhzhussupovkz@gmail.com
* 
* Permission is hereby granted, free of charge, to any person obtaining a copy of
* this software and associated documentation files (the "Software"), to deal in
* the Software without restriction, including without limitation the rights to
* use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
* the Software, and to permit persons to whom the Software is furnished to do so,
* subject to the following conditions:
* 
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
* FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
* COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
* IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
* CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class UserEchoAPI {

	//api url
	private $api_url = 'https://userecho.com/api/';

	//api key
	private $api_key;

	//constructor - Вы можете получить ключ по адресу Настройки->Дополнительно->API.
	public function __construct($api_key) {
		$this->api_key = $api_key;
	}

	//get request
	private function get_request($command = null, $params = array()) {
		$auth = array('access_token' => $this->api_key);
		$params = http_build_query($params);
		$url = $this->api_url.''.$command.'.json?'.$params;

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => 0,
		);

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		if ($result == false)
			throw new Exception(curl_error($ch));
		curl_close($ch);
		$final = json_decode($result, TRUE);
		if (!$final)
			throw new Exception('Получены неверные данные, пожалуйста, убедитесь, что запрашиваемый метод API существует');
		return $final;
	}

	//post request
	private function post_request($command = null, $params = array()) {
		$auth = array('access_token' => $this->api_key);
		$params = array_merge($auth, $params);

		$options = array(
			CURLOPT_URL => $this->api_url.''.$command.'.json',
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => 0,
			);

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		if ($result == false)
			throw new Exception(curl_error($ch));
		curl_close($ch);
		$final = json_decode($result, TRUE);
		if (!$final)
			throw new Exception('Получены неверные данные, пожалуйста, убедитесь, что запрашиваемый метод API существует');
		return $final;
	}

	/*
	get_users - Список всех пользователей проекта.
	*/
	public function get_users() {
		return $this->get_request('users');
	}

	/*
	get_users_current - Отображает сведения о профиле текущего пользователя.
	*/
	public function get_users_current() {
		return $this->get_request('users/current');
	}

	/*
	get_users_by_id - Отображает сведения о профиле пользователя.
	*/
	public function get_users_by_id($user_id = null) {
		return $this->get_request("users/$user_id");
	}

	/*
	get_users_logout - Закрыть все сессии для выбранного пользователя.
	*/
	public function get_users_logout($user_id = null) {
		return $this->get_request("users/$users_id/logout");
	}

	/*
	get_users_sso - Отображает сведения о профиле пользователя по SSO guid.
	*/
	public function get_users_sso($sso_id = null) {
		return $this->get_request("users/sso/$sso_id");
	}

	/*
	get_users_feedback - Список всех отзывов пользователя.
	*/
	public function get_users_feedback($user_id = null) {
		return $this->get_request("users/$user_id/feedback");
	}

	/*
	get_users_comments - Список всех комментариев пользователя.
	*/
	public function  get_users_comments($user_id = null) {
		return $this->get_request("users/$user_id/comments");
	}
}
