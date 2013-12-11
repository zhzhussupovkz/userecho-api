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
	private function get_request($command = null, $params = array(), $json = true) {
		$auth = array('access_token' => $this->api_key);
		$params = array_merge($auth, $params);
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
			throw new UserEchoException(curl_error($ch));
		curl_close($ch);
		if ($json == true)
			$final = json_decode($result, TRUE);
		else
			$final = $result;
		if (!$final)
			throw new UserEchoException('Получены неверные данные, пожалуйста, убедитесь, что запрашиваемый метод API существует');
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
			throw new UserEchoException(curl_error($ch));
		curl_close($ch);
		$final = json_decode($result, TRUE);
		if (!$final)
			throw new UserEchoException('Получены неверные данные, пожалуйста, убедитесь, что запрашиваемый метод API существует');
		if ($final['status'] == 'fail')
			return $final['message'];
		return $final;
	}

	/********************** Пользователи ***************************/

	/*
	get_users - Список всех пользователей проекта.
	*/
	public function get_users() {
		$result = $this->get_request('users');
		if ($result['status'] == 'success')
			return $result['users'];
	}

	/*
	get_users_current - Отображает сведения о профиле текущего пользователя.
	*/
	public function get_users_current() {
		$result = $this->get_request('users/current');
		if ($result['status'] == 'success')
			return $result['user'];
	}

	/*
	get_users_by_id - Отображает сведения о профиле пользователя.
	*/
	public function get_users_by_id($user_id = null) {
		if (!$user_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор пользователя");
		$result = $this->get_request("users/$user_id");
		if ($result['status'] == 'success')
			return $result['user'];
	}

	/*
	get_users_logout - Закрыть все сессии для выбранного пользователя.
	*/
	public function get_users_logout($user_id = null) {
		if (!$user_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор пользователя");
		return $this->get_request("users/$user_id/logout", array(), false);
	}

	/*
	get_users_sso - Отображает сведения о профиле пользователя по SSO guid.
	*/
	public function get_users_by_sso($sso_id = null) {
		if (!$sso_id)
			throw new UserEchoException("Не задан обязательный параметр: SSO guid");
		$result = $this->get_request("users/sso/$sso_id");
		if ($result['status'] == 'success')
			return $result['user'];
	}

	/*
	get_users_feedback - Список всех отзывов пользователя.
	*/
	public function get_users_feedback($user_id = null) {
		if (!$user_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор пользователя");
		$result = $this->get_request("users/$user_id/feedback");
		if ($result['status'] == 'success')
			return $result['feedbacks'];
	}

	/*
	get_users_comments - Список всех комментариев пользователя.
	*/
	public function  get_users_comments($user_id = null) {
		if (!$user_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор пользователя");
		$result = $this->get_request("users/$user_id/comments");
		if ($result['status'] == 'success')
			return $result['comments'];
	}

	/********************** Форумы ***************************/

	/*
	get_forums - Список всех топиков проекта.
	*/
	public function get_forums() {
		$result = $this->get_request("forums");
		if ($result['status'] == 'success')
			return $result['forums'];
	}

	/*
	get_forums_categories - Список категорий выбранного форума.
	*/
	public function get_forums_categories($forum_id = null) {
		if (!$forum_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор форума");
		$result = $this->get_request("forums/$forum_id/categories");
		if ($result['status'] == 'success')
			return $result['categories'];
	}

	/*
	get_forums_types - Типы поддерживаемых отзывов для выбранного форума.
	*/
	public function get_forums_types($forum_id = null) {
		if (!$forum_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор форума");
		$result = $this->get_request("forums/$forum_id/types");
		if ($result['status'] == 'success')
			return $result['types'];
	}

	/*
	get_forums_tags - Список тегов поддерживаемых выбранным форумом.
	*/
	public function get_forums_tags($forum_id = null) {
		if (!$forum_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор форума");
		$result = $this->get_request("forums/$forum_id/tags");
		if ($result['status'] == 'success')
			return $result['tags'];
	}

	/*
	create_forum - Создание нового форума

	required:
	name - Название форума

	optional:
	description - Описание форума
	type - Тип форума. (PUBLIC, PRIVATE)
	locale - Локализация форума по умолчанию (en, ru, es, fr, de, nl, is, et, kk, uk)
	allow_anonymous_feedback - Разрешить анонимные отзывы (true или false)
	allow_anonymous_votes - Разрешить анонимные голоса. (true или false)
	allow_anonymous_comments - Разрешить анонимные комментарии. (true или false)
	allow_private_sso_users - (true или false)
	allow_private_view_all - Если данный форум является приватным, позволяет всем пользователям иметь 
	доступ к форуму в режиме только для чтения. (true или false)
	template_forum_id - Копирует все настройки и вид с предоставленного форума.
	*/
	public function create_forum($name = null, $params = array()) {
		if (!$name)
			throw new UserEchoException("Не задан обязательный параметр: название форума");
		if (!is_array($params))
			throw new UserEchoException("Неверный формат параметров запроса");
		$required = array('name' => $name);
		$params = array_merge($required, $params);
		return $this->post_request("forums", $params);
	}

	/****************************** Топики *******************************/

	/*
	get_forums_feedback - Список отзывов для выбранного форума.
	*/
	public function get_forums_feedback($forum_id = null) {
		if (!$forum_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор форума");
		$result = $this->get_request("forums/$forum_id/feedback");
		if ($result['status'] == 'success')
			return $result['feedbacks'];
	}

	/*
	get_forums_user_feedback - Список отзывов, относящихся к выбранному форуму и пользователю.
	*/
	public function get_forums_user_feedback($forum_id = null, $user_id = null) {
		if (!$forum_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор форума");
		if (!$user_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор пользователя");
		$result = $this->get_request("forums/$forum_id/users/$user_id/feedback");
		if ($result['status'] == 'success')
			return $result['feedbacks'];
	}

	/*
	search_feedback - Поиск по содержимому форума.
	*/
	public function search_feedback($forum_id = null) {
		if (!$forum_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор форума");
		return $this->get_request("forums/$forum_id/feedback/search");
	}

	/*
	get_feedback_by_category - Список отзывов для выбранной категории..
	*/
	public function get_feedback_by_category($category_id = null) {
		if (!$category_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор категории");
		$result = $this->get_request("categories/$category_id/feedback");
		if ($result['status'] == 'success')
			return $result['feedbacks'];
	}

	/*
	get_feedback_info - Подробная информация по выбранному отзыву.
	*/
	public function get_feedback_info($feedback_id = null) {
		if (!$feedback_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор отзыва");
		$result = $this->get_request("feedback/$feedback_id");
		if ($result['status'] == 'success')
			return $result['feedback'];
	}

	/*
	create_feedback - Создает новый топик.

	required:
	forum_id - Идентификатор форума.
	header - Заголовок топика
	feedback_type - Тип топика.

	optional:
	description - Описание топика.
	show_voter - Показывать блок голосования. (true или false)
	*/
	public function create_feedback($forum_id = null, $header = null, $feedback_type = null, $params = array()) {
		if (!$forum_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор форума");
		if (!$header)
			throw new UserEchoException("Не задан обязательный параметр: заголовок топика");
		if (!$feedback_type)
			throw new UserEchoException("Не задан обязательный параметр: тип топика");
		if (!is_array($params))
			throw new UserEchoException("Неверный формат параметров запроса");
		$required = array('header' => $header, 'feedback_type' => $feedback_type);
		$params = array_merge($required, $params);
		return $this->post_request("forums/$forum_id/feedback", $params);
	}

	/**************************** Комментарии ***************************/

	/*
	get_feedback_comments - Список комментариев по выбранному отзыву.
	*/
	public function get_feedback_comments($feedback_id = null) {
		if (!$feedback_id)
			throw new UserEchoException("Не задан обязательный параметр: идентификатор отзыва");
		$result = $this->get_request("feedback/$feedback_id/comments");
		if ($result['status'] == 'success')
			return $result['comments'];
	}
}

class UserEchoException extends Exception {}



