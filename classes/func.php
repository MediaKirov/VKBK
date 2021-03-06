<?php

class func {
	
	function func(){
		return true;
	}
	
	function windows_path_alias($url,$type){
	    if($type == 'video'){ return preg_replace("/^Y\:\/VKBK\/video\//","/vkbk-video/",$url); }
	    if($type == 'photo'){ return preg_replace("/^K\:\/VKBK\/photo\//","/vkbk-photo/",$url); }
	    if($type == 'audio'){ return preg_replace("/^W\:\/VKBK\/music\//","/vkbk-music/",$url); }
		if($type == 'docs'){  return preg_replace("/^K\:\/VKBK\/docs\//" ,"/vkbk-docs/" ,$url); }
	}
	
	/*
	    Function: wall_attach_update
	    Inserts information about post attach to DB if attach is found in local
	    In:
	    id - attachID,
	    atk - attachData
	*/
	function wall_attach_update($id,$atk){
	    global $db;
	    
	    $type = $atk['type'];
	    
	    // Insert OR update
	    $q = $db->query("INSERT INTO `vk_attach`
	    (`uid`,`wall_id`,`type`,`is_local`,`attach_id`,`owner_id`,`uri`,`path`,`width`,`height`,`text`,`date`,`access_key`,`title`,`duration`,`player`,`link_url`,`caption`,`skipthis`)
	    VALUES
	    (NULL,{$id},'{$type}',1,{$atk[$type]['id']},0,'','',0,0,'',0,'','',0,'','','',0)
	    ON DUPLICATE KEY UPDATE
	    `wall_id` = {$id}, `type` = '{$type}', `is_local` = 1, `attach_id` = {$atk[$type]['id']}
	    ");
	}
	
	/*
	    Function: wall_attach_insert
	    Inserts information about post attach to DB if attach is NOT found in local
	    In:
	    id - attachID,
	    atk - attachData,
	    photo_uri - uri of image (photo & video) if it not stored in user albums
	*/
	function wall_attach_insert($id,$atk,$photo_uri){
	    global $db;
	    //print_r($atk);
	    $type = $atk['type'];
	    $text = '';
	    if($type == 'photo'){ $text = $atk['photo']['text']; }
	    if($type == 'video'){ $text = $atk['video']['description']; }
	    if($type == 'link'){  $text = $atk['link']['description']; }
	    
	    // Prepare empty data if another type of attach
	    $atk[$type]['width']       = !isset($atk[$type]['width'])      ? 0  : $atk[$type]['width'];
	    $atk[$type]['height']      = !isset($atk[$type]['height'])     ? 0  : $atk[$type]['height'];
	    $atk[$type]['duration']    = !isset($atk[$type]['duration'])   ? 0  : $atk[$type]['duration'];
	    $atk[$type]['title']       = !isset($atk[$type]['title'])      ? '' : $atk[$type]['title'];
	    $atk[$type]['player']      = !isset($atk[$type]['player'])     ? '' : $atk[$type]['player'];
	    $atk[$type]['url']         = !isset($atk[$type]['url'])        ? '' : $atk[$type]['url'];;
	    $atk[$type]['caption']     = !isset($atk[$type]['caption'])    ? '' : $atk[$type]['caption'];
	    $atk[$type]['access_key']  = !isset($atk[$type]['access_key']) ? '' : $atk[$type]['access_key'];
	    
	    // Save information about attach
	    $q = $db->query("INSERT INTO `vk_attach`
	    (`uid`,`wall_id`,`type`,`is_local`,`attach_id`,`owner_id`,`uri`,`path`,`width`,`height`,`text`,`date`,`access_key`,`title`,`duration`,`player`,`link_url`,`caption`,`skipthis`)
	    VALUES
	    (NULL,{$id},'{$type}',0,{$atk[$type]['id']},{$atk[$type]['owner_id']},'{$photo_uri}','',{$atk[$type]['width']},{$atk[$type]['height']},'".$db->real_escape($text)."',{$atk[$type]['date']},'{$atk[$type]['access_key']}','".$db->real_escape($atk[$type]['title'])."',{$atk[$type]['duration']},'{$atk[$type]['player']}','{$atk[$type]['url']}','".$db->real_escape($atk[$type]['caption'])."',0)
	    ON DUPLICATE KEY UPDATE
	    `wall_id` = {$id}, `type` = '{$type}', `is_local` = 0, `attach_id` = {$atk[$type]['id']}, `owner_id` = {$atk[$type]['owner_id']}, `uri` = '{$photo_uri}', `width` = {$atk[$type]['width']}, `height` = {$atk[$type]['height']}, `text` = '".$db->real_escape($text)."', `date` = {$atk[$type]['date']}, `access_key` = '{$atk[$type]['access_key']}', `title` = '".$db->real_escape($atk[$type]['title'])."', `duration` = {$atk[$type]['duration']}, `player` = '{$atk[$type]['player']}', `link_url` = '{$atk[$type]['url']}', `caption` = '".$db->real_escape($atk[$type]['caption'])."', `skipthis` = 0
	    ");
	}
	
	/*
	    Function: msg_attach_insert
	    Inserts information about message attach to DB if attach is NOT found in local
	    In:
	    id - attachID,
	    atk - attachData,
	    photo_uri - uri of image (photo & video) if it not stored in user albums
	    debug - if `true` returns array and not saving data in DB (default: false)
	*/
	function msg_attach_insert($id,$atk,$photo_uri,$debug){
	    global $db;
	    
	    $type = $atk['type'];
	    $text = '';
	    if($type == 'photo'){ $text = $atk['photo']['text']; }
	    if($type == 'video'){ $text = $atk['video']['description']; }
	    if($type == 'link'){  $text = $atk['link']['description']; }
	    
	    // Prepare empty data if another type of attach
	    $atk[$type]['width']       = !isset($atk[$type]['width'])      ? 0  : $atk[$type]['width'];
	    $atk[$type]['height']      = !isset($atk[$type]['height'])     ? 0  : $atk[$type]['height'];
	    $atk[$type]['duration']    = !isset($atk[$type]['duration'])   ? 0  : $atk[$type]['duration'];
	    $atk[$type]['title']       = !isset($atk[$type]['title'])      ? '' : $atk[$type]['title'];
	    $atk[$type]['player']      = !isset($atk[$type]['player'])     ? '' : $atk[$type]['player'];
	    $atk[$type]['url']         = !isset($atk[$type]['url'])        ? '' : $atk[$type]['url'];
	    $atk[$type]['caption']     = !isset($atk[$type]['caption'])    ? '' : $atk[$type]['caption'];
	    $atk[$type]['access_key']  = !isset($atk[$type]['access_key']) ? '' : $atk[$type]['access_key'];
	    
		// Do not update empty fields! Especially when they used later for local data :D
		$update_fields = '';
		if($atk[$type]['title'] == ''){ 	 $update_fields .= ($update_fields != '' ? ', ' : '')."`title` = '".$db->real_escape($atk[$type]['title'])."'"; }
		if($atk[$type]['player'] == ''){ 	 $update_fields .= ($update_fields != '' ? ', ' : '')."`player` = '{$atk[$type]['player']}'"; }
		if($atk[$type]['url'] == ''){ 		 $update_fields .= ($update_fields != '' ? ', ' : '')."`link_url` = '".$db->real_escape($atk[$type]['url'])."'"; }
		if($atk[$type]['caption'] == ''){ 	 $update_fields .= ($update_fields != '' ? ', ' : '')."`caption` = '".$db->real_escape($atk[$type]['caption'])."'"; }
		if($atk[$type]['access_key'] == ''){ $update_fields .= ($update_fields != '' ? ', ' : '')."`access_key` = '{$atk[$type]['access_key']}'"; }
		$update_fields = ($update_fields != '' ? ', ' : '').$update_fields;
		
	    // Save information about attach
		if($debug == false){
	    $q = $db->query("INSERT INTO `vk_messages_attach`
	    (`uid`,`wall_id`,`type`,`is_local`,`attach_id`,`owner_id`,`uri`,`path`,`width`,`height`,`text`,`date`,`access_key`,`title`,`duration`,`player`,`link_url`,`caption`,`skipthis`)
	    VALUES
	    (NULL,{$id},'{$type}',0,{$atk[$type]['id']},{$atk[$type]['owner_id']},'{$photo_uri}','',{$atk[$type]['width']},{$atk[$type]['height']},'".$db->real_escape($text)."',{$atk[$type]['date']},'{$atk[$type]['access_key']}','".$db->real_escape($atk[$type]['title'])."',{$atk[$type]['duration']},'{$atk[$type]['player']}','".$db->real_escape($atk[$type]['url'])."','".$db->real_escape($atk[$type]['caption'])."',0)
	    ON DUPLICATE KEY UPDATE
	    `wall_id` = {$id}, `type` = '{$type}', `is_local` = 0, `attach_id` = {$atk[$type]['id']}, `owner_id` = {$atk[$type]['owner_id']}, `uri` = '{$photo_uri}', `width` = {$atk[$type]['width']}, `height` = {$atk[$type]['height']}, `text` = '".$db->real_escape($text)."', `date` = {$atk[$type]['date']}, `duration` = {$atk[$type]['duration']}, `skipthis` = 0 {$update_fields}
	    ");
		} else {
			return array(
				$type => array(
		'uid'		=> NULL,
		'wall_id'	=> $id,
		'type'		=> $type,
		'is_local'	=> 0,
		'attach_id'	=> $atk[$type]['id'],
		'owner_id'	=> $atk[$type]['owner_id'],
		'uri'		=> $photo_uri,
		'path'		=> '',
		'width'		=> $atk[$type]['width'],
		'height'	=> $atk[$type]['height'],
		'text'		=> stripslashes($db->real_escape($text)),
		'date'		=> $atk[$type]['date'],
		'access_key'=> $atk[$type]['access_key'],
		'title'		=> $db->real_escape($atk[$type]['title']),
		'duration'	=> $atk[$type]['duration'],
		'player'	=> $atk[$type]['player'],
		'link_url'	=> $atk[$type]['url'],
		'caption'	=> $db->real_escape($atk[$type]['caption']),
		'skipthis'	=> 0
				)
			);
		}
	}
	
	/*
	    Function: msg_attach_update
	    Inserts information about message attach to DB if attach is found in local
	    In:
	    id - attachID,
	    atk - attachData
	*/
	function msg_attach_update($id,$atk,$debug){
	    global $db;
	    
	    $type = $atk['type'];
	    
		if($debug == false){
			// Insert OR update
			$q = $db->query("INSERT INTO `vk_messages_attach`
			(`uid`,`wall_id`,`type`,`is_local`,`attach_id`,`owner_id`,`uri`,`path`,`width`,`height`,`text`,`date`,`access_key`,`title`,`duration`,`player`,`link_url`,`caption`,`skipthis`)
			VALUES
			(NULL,{$id},'{$type}',1,{$atk[$type]['id']},0,'','',0,0,'',0,'','',0,'','','',0)
			ON DUPLICATE KEY UPDATE
			`wall_id` = {$id}, `type` = '{$type}', `is_local` = 1, `attach_id` = {$atk[$type]['id']}
			");
		} else {
			// Be lazy, Do nothing;
		}
	}
	
	/*
	    Function: wall_post_insert
	    Saves wall post and repost body to DB
	    In:
	    v - postData,
	    attach - post contains attachment
	    repost - post contains another post
	    repost_owner - owner of repost ID
	    is_repost - post are inside
	*/
	function wall_post_insert($v,$attach,$repost,$repost_owner,$is_repost){
	    global $db;
	    
	    $q = $db->query("INSERT INTO `vk_wall`
	    (`id`,`from_id`,`owner_id`,`date`,`post_type`,`text`,`attach`,`repost`,`repost_owner`,`is_repost`)
	    VALUES
	    ({$v['id']},{$v['from_id']},{$v['owner_id']},{$v['date']},'{$v['post_type']}','".$db->real_escape($this->removeEmoji($v['text']))."',{$attach},{$repost},{$repost_owner},{$is_repost})
	    ON DUPLICATE KEY UPDATE
	    `from_id` = {$v['from_id']}, `owner_id` = {$v['owner_id']}, `date` = {$v['date']}, `post_type` = '{$v['post_type']}', `text` = '".$db->real_escape($this->removeEmoji($v['text']))."', `attach` = {$attach}, `repost` = {$repost}, `repost_owner` = {$repost_owner}, `is_repost` = {$is_repost}
	    ");
	}
	
	/*
	    Function: dialog_message_insert
	    Saves message body to DB
	    In:
	    v - messageData,
	    attach - message contains attachment
	    forward - message contains additional data
	*/
	function dialog_message_insert($v,$attach,$forward,$debug){
	    global $db;
	    
		$dialog_id = $v['user_id'];
		if(!isset($v['chat_id'])){ $v['chat_id'] = 0; }
		if($v['chat_id'] > 0){ $dialog_id = 2000000000 + $v['chat_id']; }
		
		if($debug == false){
	    $q = $db->query("INSERT INTO `vk_messages`
	    (`uid`,`msg_id`,`msg_chat`,`msg_dialog`,`msg_user`,`msg_date`,`msg_body`,`msg_attach`,`msg_forwarded`)
	    VALUES
	    (null,{$v['id']},{$v['chat_id']},{$dialog_id},{$v['from_id']},'{$v['date']}','".$db->real_escape($this->removeEmoji($v['body']))."',{$attach},{$forward})
	    ON DUPLICATE KEY UPDATE
	    `msg_id` = {$v['id']}, `msg_chat` = {$v['chat_id']}, `msg_dialog` = {$dialog_id}, `msg_user` = {$v['from_id']}, `msg_date` = '{$v['date']}', `msg_body` = '".$db->real_escape($this->removeEmoji($v['body']))."', `msg_attach` = {$attach}, `msg_forwarded` = {$forward}
	    ");
		} else {
			// Be lazy, Do nothing;
		}
	}
	
	/*
	    Function: get_largest_photo
	    Returns a largest photo url
	    In: data array
	    Out: url
	*/
	function get_largest_photo($data){
	    $photo_uri = '';
	        if(isset($data['photo_2560'])){ $photo_uri = $data['photo_2560']; }
	    elseif(isset($data['photo_1280'])){ $photo_uri = $data['photo_1280'];}
	    elseif(isset($data['photo_807'])){  $photo_uri = $data['photo_807'];}
	    elseif(isset($data['photo_800'])){  $photo_uri = $data['photo_800'];} // Video
	    elseif(isset($data['photo_640'])){  $photo_uri = $data['photo_640'];} // Video
	    elseif(isset($data['photo_604'])){  $photo_uri = $data['photo_604'];}
	    elseif(isset($data['photo_320'])){  $photo_uri = $data['photo_320'];} // Video
	    elseif(isset($data['photo_130'])){  $photo_uri = $data['photo_130'];}
	    elseif(isset($data['photo_75'])){   $photo_uri = $data['photo_75'];}
	    return $photo_uri;
	}
	
	/*
	    Function: get_sticker_image
	    Returns sticker image
	    In: data - array, hq - return HQ image
	    Out: url
	*/
	function get_sticker_image($data,$hq){
	    $image = array('pre'=>"",'prew'=>0,'preh'=>0);
		
		if(isset($data['images'])){
			foreach($data as $pk => $pv){
				if($pv['width'] == '64'){
					$image['pre'] = $pv['url']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
				if($pv['width'] == '128'){
					$image['pre'] = $pv['url']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
				if($pv['width'] == '512' && $hq == true){
					$image['pre'] = $pv['url']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			}
		} else {
			if(isset($data['photo_512']) && $hq == true){ $image['pre'] = $data['photo_512'];  $image['prew'] = 512; $image['preh'] = 512; }
	    elseif(isset($data['photo_256'])){ $image['pre'] = $data['photo_256']; $image['prew'] = 256; $image['preh'] = 256;}
	    elseif(isset($data['photo_128'])){ $image['pre'] = $data['photo_128'];  $image['prew'] = 128; $image['preh'] = 128;}
	    elseif(isset($data['photo_64'])){  $image['pre'] = $data['photo_64']; $image['prew'] = 64; $image['preh'] = 64;}
		}
		
		return $image;
	}
	
	/*
	  function get_largest_doc_image
	  Returns a largest image of document preview
	  In: data array
	  Out: array(uri, width, height)
	*/
	function get_largest_doc_image($data){
		$image = array('pre'=>"",'prew'=>0,'preh'=>0);
		foreach($data as $pk => $pv){
			if(    $pv['type'] == 's'){ // 75px
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			elseif($pv['type'] == 'm'){ // 130 px
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			elseif($pv['type'] == 'x'){ // 604 px
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			elseif($pv['type'] == 'o'){ // 3:2 130 px
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			elseif($pv['type'] == 'p'){ // 3:2 200 px
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			elseif($pv['type'] == 'q'){ // 3:2 320 px
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			elseif($pv['type'] == 'r'){ // 3:2 510 px
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			elseif($pv['type'] == 'y'){ // 807 px
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			elseif($pv['type'] == 'z'){ // 1082x1024
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
			elseif($pv['type'] == 'w'){ // 2560x2048
				$image['pre'] = $pv['src']; $image['prew'] = $pv['width']; $image['preh'] = $pv['height']; }
		}
		return $image;
	}
	
	function wall_date_format($time){
	    $date = '';
	    $y = date("YYYY");
	    if($y != date("YYYY",$time)){    $date = date("d M Y H:i",$time); }
	    if($y == date("YYYY",$time)){    $date = date("d M в H:i",$time);
		$w = date("W");
		$d = date("z");
		if($w-date("W",$time) == 1){ $date = date("на этой неделе в H:i",$time); }
		if($d-date("z",$time) == 2){ $date = date("позавчера в H:i",$time); }
		if($d-date("z",$time) == 1){ $date = date("вчера в H:i",$time); }
		if($d == date("z",$time)){   $date = date("сегодня в H:i",$time);
		    $h = date("H");
		    if($h-date("H",$time) == 1){
			                     $date = "час назад";
		    }
		}
	    }
	    return $date;
	}
	
	function dialog_date_format($time){
	    $date = '';
	    $y = date("YYYY");
	    if($y != date("YYYY",$time)){    $date = date("d M Y",$time); }
	    if($y == date("YYYY",$time)){    $date = date("d M",$time);
		$w = date("W");
		$d = date("z");
		if($d-date("z",$time) == 2){ $date = date("позавчера",$time); }
		if($d-date("z",$time) == 1){ $date = date("вчера",$time); }
		if($d == date("z",$time)){   $date = date("H:i",$time);
		    $h = date("H");
		    if($h-date("H",$time) == 1){
			                     $date = "час назад";
		    }
		}
	    }
	    return $date;
	}
	
	function wall_show_post($row,$repost,$repost_body,$session){
	    global $cfg, $db, $skin;
		
	    $output = '';

	    // Load profiles for posts
	    if($row['from_id'] > 0){
		    $pr = $db->query_row("SELECT * FROM vk_profiles WHERE `id` = ".$row['from_id']);
        	$path = 'profiles';
		    $who = $pr['first_name'].' '.$pr['last_name'];
	    } else {
		    $pr = $db->query_row("SELECT * FROM vk_groups WHERE `id` = ".abs($row['from_id']));
		    $path = 'groups';
		    $who = $pr['name'];
	    }

	    // Attachments
	    $attach = array(
		'local_photo'  => '',
		'attach_photo' => '',
		'local_video'  => '',
		'attach_video' => '',
		'attach_link'  => '',
		'local_audio'  => '',
		'attach_audio' => '',
		'local_doc'    => '',
		'attach_doc'   => ''
	    );
	
	    if($row['attach'] == 1){
		$q = $db->query("SELECT * FROM vk_attach WHERE wall_id = ".$row['id']);
		while($at_row = $db->return_row($q)){
			if($at_row['type'] == 'photo' && $at_row['is_local'] == 1){
				$attach['local_photo'] .= ($attach['local_photo'] != '' ? ',' : '').$at_row['attach_id'];
			}
			if($at_row['type'] == 'photo' && $at_row['is_local'] == 0 && $at_row['path'] != ''){
				$attach['attach_photo'] .= ($attach['attach_photo'] != '' ? ',' : '').$at_row['attach_id'];
			}
			if($at_row['type'] == 'video' && $at_row['is_local'] == 1){
				$attach['local_video'] .= ($attach['local_video'] != '' ? ',' : '').$at_row['attach_id'];
			}
			if($at_row['type'] == 'video' && $at_row['is_local'] == 0 && $at_row['path'] != ''){
				$attach['attach_video'] .= ($attach['attach_video'] != '' ? ',' : '').$at_row['attach_id'];
			}
			if($at_row['type'] == 'link'){
				$attach['attach_link'] .= ($attach['attach_link'] != '' ? ',' : '').$at_row['attach_id'];
			}
			if($at_row['type'] == 'audio' && $at_row['is_local'] == 1){
				$attach['local_audio'] .= ($attach['local_audio'] != '' ? ',' : '').$at_row['attach_id'];
			}
			if($at_row['type'] == 'audio' && $at_row['is_local'] == 0 && $at_row['path'] != ''){
				$attach['attach_audio'] .= ($attach['attach_audio'] != '' ? ',' : '').$at_row['attach_id'];
			}
			if($at_row['type'] == 'doc' && $at_row['is_local'] == 1){
				$attach['local_doc'] .= ($attach['local_doc'] != '' ? ',' : '').$at_row['attach_id'];
			}
			if($at_row['type'] == 'doc' && $at_row['is_local'] == 0 && $at_row['player'] != ''){
				$attach['attach_doc'] .= ($attach['attach_doc'] != '' ? ',' : '').$at_row['attach_id'];
			}
		}
	    }

	    $full_date = date("d M Y H:i",$row['date']);
	    $row['date'] = $this->wall_date_format($row['date']);
	    if($row['text'] != ''){ $row['text'] = '<div class="mb-2">'.nl2br($this->wall_post_parse($row['text'])).'</div>'; }
	    $tmp_box = '';
	    $tmp_class = 'col-sm-12 col-md-6 m-auto wall-box';
$tmp_postid = <<<E
    <a class="post-id wallious fancybox" data-fancybox data-type="iframe" data-title-id="#{$row['id']}" href="ajax/wall-post.php?p={$row['id']}" onClick="javascript:urlCommands.urlPush({post:{$row['id']}});">#{$row['id']}</a>
E;
	    if($repost === true){
		$tmp_box = 'repost';
		$tmp_class = 'col-sm-12 repost-box';
	    }
	    if($repost === 'single'){
		$tmp_class = 'col-sm-12 wall-box';
		$tmp_postid = '';
	    }

$output .= <<<E
<div class="row mb-2 {$tmp_box}">
    <div class="{$tmp_class}">
	{$tmp_postid}
	<img src="data/{$path}/{$pr['photo_path']}" class="wall-ava" />
	<div class="wall-head">
		<a href="javascript:;">{$who}</a><br/><span class="full-date" data-placement="right" data-toggle="tooltip" data-original-title="{$full_date}">{$row['date']}</span>
	</div>
	<div style="clear:both;"></div>
	{$row['text']}{$repost_body}
E;


foreach($attach as $qk => $qv){

$attach_query = false;
$qclass = '';
if($qk == 'local_photo' && $qv != '' && $row['owner_id'] == $session['vk_user']){
	$q = $db->query("SELECT * FROM vk_photos WHERE id IN(".$qv.")");
	$attach_query = true;
	$qclass = 'free-wall';
}
if($qk == 'attach_photo' && $qv != ''){
	$q = $db->query("SELECT * FROM vk_attach WHERE attach_id IN(".$qv.") AND wall_id = ".$row['id']." AND owner_id = ".$row['owner_id']);
	$attach_query = true;
	$qclass = 'free-wall';
}

if($qk == 'local_video' && $qv != '' && $row['owner_id'] == $session['vk_user']){
	$q = $db->query("SELECT * FROM vk_videos WHERE id IN(".$qv.")");
	$attach_query = true;
}
if($qk == 'attach_video' && $qv != ''){
	$q = $db->query("SELECT * FROM vk_attach WHERE attach_id IN(".$qv.") AND wall_id = ".$row['id']." AND owner_id = ".$row['owner_id']);
	$attach_query = true;
}

if($qk == 'attach_link' && $qv != ''){
	$q = $db->query("SELECT * FROM vk_attach WHERE attach_id IN(".$qv.") AND wall_id = ".$row['id']." AND owner_id = ".$row['owner_id']);
	$attach_query = true;
}

if($qk == 'local_audio' && $qv != '' && $row['owner_id'] == $session['vk_user']){
	$q = $db->query("SELECT * FROM vk_music WHERE id IN(".$qv.")");
	$attach_query = true;
}
if($qk == 'attach_audio' && $qv != ''){
	$q = $db->query("SELECT * FROM vk_attach WHERE attach_id IN(".$qv.") AND wall_id = ".$row['id']." AND owner_id = ".$row['owner_id']);
	$attach_query = true;
}
if($qk == 'local_doc' && $qv != '' && $row['owner_id'] == $session['vk_user']){
	$q = $db->query("SELECT * FROM vk_docs WHERE id IN(".$qv.")");
	$attach_query = true;
}
if($qk == 'attach_doc' && $qv != ''){
	$q = $db->query("SELECT * FROM vk_attach WHERE attach_id IN(".$qv.") AND wall_id = ".$row['id']." AND owner_id = ".$row['owner_id']);
	$attach_query = true;
}
    
if($attach_query == true){
	$output .= '<div class="'.$qclass.'">';
	while($lph_row = $db->return_row($q)){
		// Let's try to guess what type of data we have received
		
		// Type - Photo or attach photo
		if((isset($lph_row['type']) && $lph_row['type'] == 'photo') || isset($lph_row['album_id'])){
			// Rewrite for Alias
			if($cfg['vhost_alias'] == true && substr($lph_row['path'],0,4) != 'http'){
				$lph_row['path'] = $this->windows_path_alias($lph_row['path'],'photo');
			}
$output .= <<<E
    <div class="brick" style='width:{$cfg['wall_layout_width']}px;'><a class="fancybox" data-fancybox="images" rel="p{$row['id']}" href="{$lph_row['path']}"><img style="width:100%" src="{$lph_row['path']}"></a></div>
E;
		} // end of attach photo
		
		// Remote Video Attach
		if(isset($lph_row['type']) && $lph_row['type'] == 'video'){
			// Rewrite for Alias
			if($cfg['vhost_alias'] == true && substr($lph_row['path'],0,4) != 'http'){
				$lph_row['path'] = $this->windows_path_alias($lph_row['path'],'video');
			}
			
			// Clean player
			$lph_row['player'] = $this->clean_player($lph_row['player']);

			if($lph_row['text'] != ''){ $lph_row['text'] = '<div style="margin-bottom:10px;">'.nl2br($lph_row['text']).'</div>'; }
			$lph_row['duration'] = $skin->seconds2human($lph_row['duration']);
$output .= <<<E
	<div class="wall-video-box">
	    <span class="label label-default wall-video-duration">{$lph_row['duration']}</span>
	    <a class="various fancybox" href="javascript:;" onclick="javascript:fbox_video_global('{$lph_row['player']}',1);" data-title-id="title-{$lph_row['attach_id']}" style="background-image:url('{$lph_row['path']}');"></a>
	</div>
	<h6 class="wall-video-header">{$lph_row['title']}</h6>
	<div id="title-{$lph_row['attach_id']}" style="display:none;">
	    {$lph_row['text']}
	    <div class="expander" onClick="expand_desc();">показать</div>
	</div>
E;
		} // end of attach video
		
		// Remote Link Attach
		if(isset($lph_row['type']) && $lph_row['type'] == 'link'){
			// Rewrite for Alias
			if($cfg['vhost_alias'] == true && substr($lph_row['path'],0,4) != 'http'){
				$lph_row['path'] = $this->windows_path_alias($lph_row['path'],'photo');
			}

			if($lph_row['text'] != ''){ $lph_row['text'] = nl2br($lph_row['text']); }
			if($lph_row['path'] != ''){
$output .= <<<E
    <div class="wall-link-img"><a class="fancybox" data-fancybox="images" rel="p{$row['id']}" href="{$lph_row['path']}"><img style="width:100%" src="{$lph_row['path']}"></a><a href="{$lph_row['link_url']}" class="wall-link-caption" rel="nofollow noreferrer" target="_blank"><i class="fa fa-link"></i>&nbsp;{$lph_row['caption']}</a></div>
E;
$output .= <<<E
<div class="col-sm-12" style="border:1px solid rgba(0,20,51,.12);">
	<h6>{$lph_row['title']}</h6>
	<p class="wall-description">{$lph_row['text']}</p>
</div>
E;
			} else {
$output .= <<<E
<div class="col-sm-12">
	<h5><a href="{$lph_row['link_url']}" rel="nofollow noreferrer" target="_blank"><i class="fas fa-share"></i> {$lph_row['title']}</a></h5>
	<p class="wall-description">{$lph_row['text']}</p>
</div>
E;
			}

		} // end of attach link
		
		// Remote Audio Attach
		if(isset($lph_row['type']) && $lph_row['type'] == 'audio'){
			// Rewrite for Alias
			if($cfg['vhost_alias'] == true && substr($lph_row['path'],0,4) != 'http'){
				$lph_row['path'] = $this->windows_path_alias($lph_row['path'],'audio');
			}
			
			if($lph_row['path'] != ''){
$output .= <<<E
<div class="col-sm-12" style="margin:4px auto 0 auto;font-size:12px;">
    {$lph_row['caption']} - {$lph_row['title']}
    <audio controls preload="none" style="width:100%;">
	<source src="{$lph_row['path']}" type="audio/mpeg">
	Ваш браузер не поддерживает HTML5 аудио.
    </audio>
</div>
E;
			}

		} // end of attach audio
		
		// Type - Document or attach document
		if((isset($lph_row['type']) && $lph_row['type'] == 'doc')){
			// Rewrite for Alias
			if($cfg['vhost_alias'] == true && substr($lph_row['path'],0,4) != 'http'){
				$lph_row['path'] = $this->windows_path_alias($lph_row['path'],'docs');
			}
			// Attach
			if(isset($lph_row['player'])){
				// Have preview
				if($lph_row['path'] != ''){
					// Rewrite for Alias
					if($cfg['vhost_alias'] == true && substr($lph_row['player'],0,4) != 'http'){
						$lph_row['player'] = $this->windows_path_alias($lph_row['player'],'docs');
					}
					$animated = '';
					if(strtolower(substr($lph_row['player'],-3)) == "gif"){
						$animated = 'class="doc-gif" data-docsrc="'.$lph_row['player'].'" data-docpre="'.$lph_row['path'].'"';
					}
$output .= <<<E
    <div class="brick" style='width:100%;'><a class="fancybox" data-fancybox="images" rel="p{$row['id']}" href="{$lph_row['player']}"><img {$animated} style="width:100%" src="{$lph_row['path']}"></a></div>
E;
				} else {
					$lph_row['duration'] = $this->human_filesize($lph_row['duration']);
					$lph_row['caption'] = strtoupper($lph_row['caption']);
$output .= <<<E
<div class="col-sm-12">
	<h5><a href="{$lph_row['player']}" rel="nofollow noreferrer" target="_blank"><i class="fas fa-share"></i> {$lph_row['title']}</a></h5>
	<p class="wall-description"><span class="label label-default">{$lph_row['caption']}</span> {$lph_row['duration']}</p>
</div>
E;
				}
			}
			
		} // end of attach document
		
	}
	$output .= '</div><div style="clear:both;"></div>';
}

} // Foreach $attach end

$output .= <<<E
	</div>
</div>
E;
	    return $output;
	} // Wall Show Post end
	
	function wall_post_parse($text){
	    if($text != ''){
		$fnd = array(
		    '/\#([^\s\#]+)/',
		    '/\[([^\s\|]+)\|([^\]]+)\]/'
		);
		$rpl = array(
		    '<a href="https://new.vk.com/feed?section=search&q=%23\1" rel="norefferer" target="_blank"><i class="fa fa-tag"></i> \1</a>',
		    '<a href="https://new.vk.com/\1" rel="norefferer" target="_blank"><i class="fa fa-link"></i> \2</a>'
		);
		$text = preg_replace($fnd,$rpl,$text);
		
		return $text;
	    } else {
		return false;
	    }
	}
	
	/*
	    Function: dialog_insert
	    Saves dialog to DB
	    In:
	    v - dialogData,
	    multi - multichat array:
	    on - bool, chat_id - int, users - int, admin - int;
	    ex - existing dialogs ID's array:
	    dialog_id => in_read
	*/
	function dialog_insert($v,$multi,$ex){
	    global $db;
		
		$is_new = 0;
		$is_upd = 0;
		$cid = 0;
		// Set chat_id if exist
		if($v['conversation']['peer']['type'] == 'chat'){
			$cid = $v['conversation']['peer']['local_id'];
		}
		
		// Override `new` and `upd` if they already set
		if(isset($ex[$v['conversation']['peer']['id']][$cid]['new']) && $ex[$v['conversation']['peer']['id']][$cid]['new'] == 1){ $is_new = 1; }
		if(isset($ex[$v['conversation']['peer']['id']][$cid]['upd']) && $ex[$v['conversation']['peer']['id']][$cid]['upd'] == 1){ $is_upd = 1; }
		
		// Check existance of dialog
		if(isset($ex[$v['conversation']['peer']['id']][$cid])){
			// Compare inRead and chatId values to see do we have a new messages
			if($ex[$v['conversation']['peer']['id']][$cid]['read'] < $v['conversation']['in_read'] && $ex[$v['conversation']['peer']['id']][$cid]['chat'] == $cid){
				$is_upd = 1;
			}
		} else {
			$is_new = 1;
		}
		
		$title = '';
		if(isset($v['conversation']['chat_settings']['title'])){
			$title = $db->real_escape($this->removeEmoji($v['conversation']['chat_settings']['title']));
		}
		
	    $db->query("INSERT INTO `vk_dialogs`
	    (`id`,`date`,`title`,`in_read`,`multichat`,`chat_id`,`admin_id`,`users`,`is_new`,`is_upd`)
	    VALUES
	    ({$v['conversation']['peer']['id']},{$v['last_message']['date']},'{$title}',{$v['conversation']['in_read']},{$multi['on']},{$multi['chat_id']},{$multi['admin']},{$multi['users']},{$is_new},{$is_upd})
	    ON DUPLICATE KEY UPDATE
	    `id` = {$v['conversation']['peer']['id']}, `date` = {$v['last_message']['date']}, `title` = '{$title}', `in_read` = {$v['conversation']['in_read']}, `multichat` = {$multi['on']},  `chat_id` = {$multi['chat_id']}, `admin_id` = {$multi['admin']}, `users` = {$multi['users']}, `is_new` = {$is_new}, `is_upd` = {$is_upd}
	    ");
	}
	
	/*
	    Function: is_html_response
	    Check response to html data
	    In:
	    response - cURL response data
	*/
	function is_html_response($response){
		if((substr($response,0,5) == '<html') || (substr($response,0,9) == '<!DOCTYPE')){
			return true;
		} else {
			return false;
		}
	}
	
	/*
		Human File Size
		Converts bytes to more understandable values
	*/
	function human_filesize($bytes, $decimals = 2) {
		$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
	
	/*
		Human Thousand
		Returns a short value for thousands
		In:
		num - value (int)
		Out:
		false\null or string
	*/
	function human_thousand($num) {
		if(!is_numeric($num)){
			return false;
		} else {
			if($num >= 1000){
				return sprintf("%.1f", $num / 1000) . "k";
			}  else {
				return $num;
			}
		}
	}
	
	/*
		Emoji clecn function by quantizer
		https://gist.github.com/quantizer/5744907
	*/
	function removeEmoji($text) {
        $cleanText = "";

        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $cleanText = preg_replace($regexEmoticons, '', $text);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $cleanText = preg_replace($regexSymbols, '', $cleanText);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $cleanText = preg_replace($regexTransport, '', $cleanText);

        return $cleanText;
	}
	
	/*
		Disable annotations in YouTube player & other cleaning
		In:
		link - player link (string)
		Out:
		string
	*/
	function clean_player($link){
		$link = preg_replace("/\?__ref\=vk\.api/", "", $link);
		
		if(strstr($link,'youtube.com') || strstr($link,'youtu.be')){
			return $link.'?iv_load_policy=3';
		} else {
			return $link;
		}
	}
	
	/*
		Return a row from array for debug purposes
		In:
		p - array(params,params) (multi array)
		head - return first as <th> or <td> (bool)
		Out:
		string
	*/
	function dbg_row($p,$head){
		$output = '<table cellpadding="2" border="1" style="border-collapse:collapse;">';
		foreach($p as $k => $v){
			if(is_array($v)){
				$output .= '<tr>';
				foreach($v as $a => $b){
					$output .= ($head == true && $k == 0 ? '<th>' : '<td>').$b.($head == true && $k == 0 ? '</th>' : '</td>');
				}
				$output .= '</tr>';
			}
		}
		$output .= '</table>';
		return $output;
	}

} // end of class

?>