<?php
// Version: 0.1; PostHistory

function template_list_edits()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<div class="tborder">
		<div class="headerpadding titlebg">', $context['ph_topic']['msg_subject'], '</div>
		<table border="0" cellspacing="1" cellpadding="4" align="center" width="100%" class="bordercolor">
			<tr class="catbg3">
				<td>', $txt['ph_last_edit'], '</td>
				<td>', $txt['ph_last_time'], '</td>
				<td>', $txt['ph_view_edit'], '</td>
			</tr>';
	
	// First we check if moderators have been lazy
	if (empty($context['post_history']))
		echo '
		<tr class="catbg"><td colspan="3">', $txt['ph_no_edits'], '</td></tr>';
	else
	{
		$alternate = false;
		
		foreach ($context['post_history'] as $edit)
		{
			echo '
			<tr class="windowbg', $alternate ? '2' : '', '">
				<td>', $edit['name'], '</td>
				<td>
					', $edit['time'], '
					', $edit['is_current'] || $edit['is_original'] ? '(' . $txt['ph_' . ($edit['is_current'] ? 'current_' : '') . ($edit['is_original'] ? 'original_' : '') . 'edit'] . ')' : '', '
				</td>
				<td><a href="', $edit['href'], '">', $txt['ph_view_edit'], '</a></td>
			</tr>';
				
			$alternate = !$alternate;
		}
	}
	
	echo '
		</table>
	</div>';
}

function template_list_edits_popup()
{
	global $context, $settings, $options, $txt;

	// Since this is a popup of its own we need to start the html, etc.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
		<meta name="robots" content="noindex" />
		<title>', $context['page_title'], '</title>
		<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css" />
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js"></script>
	</head>
	<body id="edits_popup">
		<div class="windowbg description">
		<span class="topslice"><span></span></span>';
	
	echo '
	<div class="tborder">
		<div class="headerpadding titlebg">', $context['ph_topic']['msg_subject'], '</div>
		<table border="0" cellspacing="1" cellpadding="4" align="center" width="100%" class="bordercolor">
			<tr class="catbg3">
				<td>', $txt['ph_last_edit'], '</td>
				<td>', $txt['ph_last_time'], '</td>
				<td>', $txt['ph_view_edit'], '</td>
			</tr>';
	
	// First we check if moderators have been lazy
	if (empty($context['post_history']))
		echo '
		<tr class="catbg"><td colspan="3">', $txt['ph_no_edits'], '</td></tr>';
	else
	{
		$alternate = false;
		
		foreach ($context['post_history'] as $edit)
		{
			echo '
			<tr class="windowbg', $alternate ? '2' : '', '">
				<td>', $edit['name'], '</td>
				<td>
					', $edit['time'], '
					', $edit['is_current'] || $edit['is_original'] ? '(' . $txt['ph_' . ($edit['is_current'] ? 'current_' : '') . ($edit['is_original'] ? 'original_' : '') . 'edit'] . ')' : '', '
				</td>
				<td><a href="', $edit['href'], '">', $txt['ph_view_edit'], '</a></td>
			</tr>';
				
			$alternate = !$alternate;
		}
	}
	
	echo '
		</table>
	</div>';
	
	echo '
			
			<br />
			<a href="javascript:self.close();">', $txt['close_window'], '</a>
			<span class="botslice"><span></span></span>
		</div>
	</body>
</html>';
}

function template_view_edit()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<div class="tborder">
		<div class="headerpadding titlebg">', $context['ph_topic']['msg_subject'], '</div>
		<div class="headerpadding catbg3">', $txt['ph_last_edit'], ': ', $context['current_edit']['name'], ' (', $context['current_edit']['time'], ')</div>
		<div class="smallpadding windowbg2">
			', $context['current_edit']['body'], '
		</div>
	</div>';
}

function template_view_edit_popup()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<div class="tborder">
		<div class="headerpadding titlebg">', $context['ph_topic']['msg_subject'], '</div>
		<div class="headerpadding catbg3">', $txt['ph_last_edit'], ': ', $context['current_edit']['name'], ' (', $context['current_edit']['time'], ')</div>
		<div class="smallpadding windowbg2">
			', $context['current_edit']['body'], '
		</div>
	</div>';
}

?>