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
			t.id_topic, ms.subject, mc.subject AS msg_subject, mc.id_member, mc.body,
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
				'href' => $scripturl . '?action=posthistory;topic=' . $topic . '.0;msg=' . $_REQUEST['msg'] . ';edit=' . $row['id_edit'] . (isset($_REQUEST['popup']) ? ';popup' : ''),
				'name' => $row['modified_name'],
				'time' => timeformat($row['modified_time']),
				'is_original' => $row['modified_time'] == $context['ph_topic']['poster_time'],
				'is_current' => false,
			);
		$smcFunc['db_free_result']($request);
		
		$context['post_history']['current'] = array(
			'id' => 'current',
			'href' => $scripturl . '?action=posthistory;topic=' . $topic . '.0;msg=' . $_REQUEST['msg'] . ';edit=current' . (isset($_REQUEST['popup']) ? ';popup' : ''),
			'name' => !empty($context['ph_topic']['modified_name']) ? $context['ph_topic']['modified_name'] : $context['ph_topic']['poster_name'],
			'time' => timeformat(!empty($context['ph_topic']['modified_time']) ? $context['ph_topic']['modified_time'] : $context['ph_topic']['poster_time']),			
			'is_original' => empty($context['ph_topic']['modified_time']),
			'is_current' => true,
		);
		
		$context['sub_template'] = 'list_edits' . (isset($_REQUEST['popup']) ? '_popup' : '');
	}
	else
	{
		// Sanitise numbers
		if (is_numeric($_REQUEST['edit']))
			$_REQUEST['edit'] = (int) $_REQUEST['edit'];
		
		$context['current_edit'] = loadEdit($context['ph_topic'], $_REQUEST['edit'], $_REQUEST['msg'], !isset($_REQUEST['compare_to']));
		
		if (isset($_REQUEST['compare_to']))
			$context['compare_edit'] = loadEdit($context['ph_topic'], (int) $_REQUEST['compare_to'], $_REQUEST['msg'], false);
		
		if (!$context['current_edit'] || (isset($context['compare_edit']) && !$context['compare_edit']))
			fatal_lang_error('not_a_topic');
			
		if (!isset($context['compare_edit']))
			$context['sub_template'] = 'view_edit' . (isset($_REQUEST['popup']) ? '_popup' : '');
		else
		{
			$context['edit_changes'] = __diff(
				preg_split('@(\[|\]|=| |[\s, ]|<br />)@', $context['compare_edit']['body'], null, PREG_SPLIT_DELIM_CAPTURE),
				preg_split('@(\[|\]|=| |[\s, ]|<br />)@', $context['current_edit']['body'], null, PREG_SPLIT_DELIM_CAPTURE)
			);
			$context['sub_template'] = 'compare_edit' . (isset($_REQUEST['popup']) ? '_popup' : '');
		}
	}

	// Template
	if (isset($_REQUEST['popup']))
	{
		$context['template_layers'] = array();
		loadLanguage('Help');
	}

	loadTemplate('PostHistory');
	
	$context['page_title'] = sprintf($txt['title_view_post_history'], $context['ph_topic']['msg_subject']);
}

function loadEdit($topic, $id_edit, $id_msg = 0, $parse = true)
{
	global $smcFunc, $scripturl;
	
	if (is_int($id_edit))
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_edit, id_msg, modified_name, modified_time, body
			FROM {db_prefix}messages_history
			WHERE id_edit = {int:edit}'. (!empty($id_msg) ? '
				AND id_msg = {int:msg}' : ''),
			array(
				'msg' => $id_msg,
				'edit' => $id_edit,
			)
		);
		
		$row = $smcFunc['db_fetch_assoc']($request);
		
		if (!$row)
			return false;
		
		$smcFunc['db_free_result']($request);
		
		return array(
			'id' => $row['id_edit'],
			'href' => $scripturl . '?action=posthistory;topic=' . $topic['id_topic'] . '.0;msg=' . $row['id_msg'] . ';edit=' . $row['id_edit'] . (isset($_REQUEST['popup']) ? ';popup' : ''),
			'name' => $row['modified_name'],
			'time' => timeformat($row['modified_time']),
			'body' => $parse ? parse_bbc($row['body']) : $row['body'],
		);
	}
	elseif ($id_edit == 'current')
		return array(
			'id' => 'current',
			'href' => $scripturl . '?action=posthistory;topic=' . $topic['id_topic'] . '.0;msg=' . $id_msg . ';edit=current' . (isset($_REQUEST['popup']) ? ';popup' : ''),
			'name' => !empty($topic['modified_name']) ? $topic['modified_name'] : $topic['poster_name'],
			'time' => timeformat(!empty($topic['modified_time']) ? $topic['modified_time'] : $topic['poster_time']),			
			'body' => $parse ? parse_bbc($topic['body']) : $topic['body'],
		);
	
	return false;
}

function __diff($old, $new)
{
	$maxlen = 0;

	foreach($old as $oindex => $ovalue)
	{
		$nkeys = array_keys($new, $ovalue);
		foreach($nkeys as $nindex)
		{
			$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
				$matrix[$oindex - 1][$nindex - 1] + 1 : 1;
			if ($matrix[$oindex][$nindex] > $maxlen)
			{
				$maxlen = $matrix[$oindex][$nindex];
				$omax = $oindex + 1 - $maxlen;
				$nmax = $nindex + 1 - $maxlen;
			}
		}
	}

	if ($maxlen == 0)
		return array(
			array('d' => $old, 'i'=> $new)
		);

	return array_merge(
		__diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
		array_slice($new, $nmax, $maxlen),
		__diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen))
	);
}

?>