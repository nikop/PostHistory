<?php

if (!defined('SMF'))
	die('Hacking attempt...');

function PostHistory()
{
	global $context, $scripturl, $smcFunc, $topic, $user_info, $txt;
	
	if (empty($topic) || empty($_REQUEST['msg']))
		fatal_lang_error('not_a_topic');
		
	// Content is mostly duplicates so no indexing
	$context['robot_no_index'] = true;
	
	// Make sure message is integer	
	$_REQUEST['msg'] = (int) $_REQUEST['msg'];

	// Try to get topic from cache, this checks that msg is actually in topic that user said it to be
	if (($real_topic = cache_get_data('msg_topic-' . $_REQUEST['msg'], 120)) === null)
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_topic
			FROM {db_prefix}messages
			WHERE id_msg = {int:id_msg}
			LIMIT 1',
			array(
				'id_msg' => $_REQUEST['msg'],
			)
		);

		// So did it find anything?
		if ($smcFunc['db_num_rows']($request))
		{
			list ($real_topic) = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);
					
			// Save save save.
			cache_put_data('msg_topic-' . $_REQUEST['msg'], $real_topic, 120);
		}
		else
			fatal_lang_error('not_a_topic');
	}

	if ($topic != $real_topic)
		fatal_lang_error('not_a_topic');
		
	// Get all the important topic info.
	$request = $smcFunc['db_query']('', '
		SELECT
			ms.subject, mc.subject AS msg_subject, mc.id_member, mc.body,
			mc.modified_name, mc.modified_time, mc.poster_time,
			IFNULL(mem.real_name, mc.poster_name) AS poster_name
		FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}messages AS ms ON (ms.id_msg = t.id_first_msg)
			INNER JOIN {db_prefix}messages AS mc ON (mc.id_msg = {int:id_msg})
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = mc.id_member)
		WHERE t.id_topic = {int:current_topic}
		LIMIT 1',
		array(
			'current_topic' => $topic,
			'id_msg' => $_REQUEST['msg'],
		)
	);
	if ($smcFunc['db_num_rows']($request) == 0)
		fatal_lang_error('not_a_topic', false);
	$context['ph_topic'] = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	
	if (empty($context['ph_topic']['id_member']) || $context['ph_topic']['id_member'] != $user_info['id'] || !allowedTo('posthistory_view_own'))
		isAllowedTo('posthistory_view_any');
		
	// Edit wasnt selected
	if (!isset($_REQUEST['edit']))
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_edit, modified_name, modified_time
			FROM {db_prefix}messages_history
			WHERE id_msg = {int:id_msg}',
			array(
				'id_msg' => $_REQUEST['msg'],
			)
		);
		
		$context['post_history'] = array();
		
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$context['post_history'][$row['id_edit']] = array(
				'id' => $row['id_edit'],
				'href' => $scripturl . '?action=posthistory;topic=' . $topic . '.0;msg=' . $_REQUEST['msg'] . ';edit=' . $row['id_edit'],
				'name' => $row['modified_name'],
				'time' => timeformat($row['modified_time']),
				'is_original' => $row['modified_time'] == $context['ph_topic']['poster_time'],
				'is_current' => false,
			);
		$smcFunc['db_free_result']($request);
		
		$context['post_history']['current'] = array(
			'id' => 'current',
			'href' => $scripturl . '?action=posthistory;topic=' . $topic . '.0;msg=' . $_REQUEST['msg'] . ';edit=current',
			'name' => !empty($context['ph_topic']['modified_name']) ? $context['ph_topic']['modified_name'] : $context['ph_topic']['poster_name'],
			'time' => timeformat(!empty($context['ph_topic']['modified_time']) ? $context['ph_topic']['modified_time'] : $context['ph_topic']['poster_time']),			
			'is_original' => empty($context['ph_topic']['modified_time']),
			'is_current' => true,
		);
	}
	// Viewing single edit
	else
	{
		// Make sure edit is integer
		$_REQUEST['edit'] = (int) $_REQUEST['edit'];
		
		$request = $smcFunc['db_query']('', '
			SELECT id_edit, modified_name, modified_time, body
			FROM {db_prefix}messages_history
			WHERE id_msg = {int:id_msg}
				AND id_edit = {int:edit}',
			array(
				'id_msg' => $_REQUEST['msg'],
				'edit' => $_REQUEST['edit'],
			)
		);
		
		$row = $smcFunc['db_fetch_assoc']($request);
		
		if (!$row)
			fatal_lang_error('not_a_topic');
		
		$context['current_edit'] = array(
			'id' => $row['id_edit'],
			'href' => $scripturl . '?action=posthistory;topic=' . $topic . ';msg=' . $_REQUEST['msg'] . ';edit=' . $row['id_edit'],
			'name' => $row['modified_name'],
			'time' => timeformat($row['modified_time']),
			'body' => parse_bbc($row['body']),
		);
			
		$smcFunc['db_free_result']($request);
		
		$context['sub_template'] = 'view_edit';
	}
	
	loadTemplate('PostHistory');
	$context['page_title'] = sprintf($txt['title_view_post_history'], $context['ph_topic']['msg_subject']);
}

?>