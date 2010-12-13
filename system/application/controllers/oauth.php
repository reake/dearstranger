<?php

	class OAuth extends KK_Controller {
		
		
		/**
		 *	OAuth Callback返回
		 
		 		检查用户ID是否存在数据库, 不存在, redirect到第一次登录页
		 								存在,   提示成功!
		 		
		 */
		function index() {
			$this->load->library('T_sina');
			$this->load->model('user_model');
			
			$last_key = $this->t_sina->getAccessToken();
			
			if ( $last_key ) {
				// oauth 登录成功~
				$this->session->set_userdata('last_key', $last_key );
				
				// 获取登录帐户的个人资料
				$weibo = $this->t_sina->getWeibo();
				$me = $weibo->verify_credentials();
				
				// 首先要验证 $me 是否登录了的～～
				if ( isset( $me['error_code'] ) ) {
					// 未登录~，session可能滞留了。清空它
					$this->session->unset_userdata('last_key');
					
					exit('failed! have logined');
					
					// 清空登录数据之后，重新获取一次
					$weibo = $this->t_sina->getWeibo();
					$me = $weibo->verify_credentials();
				}
				
				/** 
				 * 判断微博用户是否已登录过, 未登录过, 创建用户~
				 */
				if ( !$this->user_model->is_user_existed( array( 't_sina_id'=>$me['id'] ) ) ) {
					
					
					$user_id = $this->user_model->create_user( $me['id'] );
					
					// 创建用户后, 填写创建profile
					
					// 微博提供的头像图片太小了， 改成180的
					$image_url = str_replace( '/50/', '/180/', $me['profile_image_url']);
					$this->user_model->create_or_update_user_profile( $user_id, array(
						'nickname' => $me['screen_name'],
						'province_id' => $me['province'],
						'city_id' => $me['city'],
						'description' => $me['description'],
						'gender' => ( $me['gender'] == 'm' ) ? '男' : '女',
						'image_url' => $image_url,
						'website' => $me['url'],
					) );
					
					
					// 用户授权成功，回到首页
					redirect('/');
					echo( 'user not existed, creating <br />');
					
				} else {
					/**
					 *	用户存在了, 转到测试页
					 			但为创建user_profile~ 提供资料填写~
					 */
					 
					 
					 
					redirect( '/' );
					
				}
				
			} else {
				exit( ' oauth failed!?' );
			}
			
			//echo 'success, go test? <a href="oauth/test">Test!</a>';

		}
		
		
		function test() {
			$this->load->library('T_sina');
			// $weibo = $this->t_sina->getWeibo();
// 			$timeline = $weibo->user_timeline(); //$weibo->reply('test api weibo');
// 			print_r( $timeline );
// 			$first_wb = $timeline[0];

			$this->t_sina->reply_last_wb('that~ma ' );
			
			
// 			$this->load->library('T_sina');
// 			$weibo = $this->t_sina->getWeibo();
// 			$ms  = $weibo->home_timeline(); // done
// 			
// 			$me = $weibo->verify_credentials(); // 获取自己的信息
// 			if ( isset( $me['error_code'] ) ) {
// 				
// 				echo sprintf('<a href="%s">授权</a>', $this->t_sina->getAuthorizeURL( oauth_callback() ) );
// 			}
// 			print_r( $me );
		}
		
		
		function logout() {
			$this->session->unset_userdata('last_key');
			// 返回首页
			redirect('/');
			echo 'logout!';
		}		
	}