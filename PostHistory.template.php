<?php
// Version: 0.1; PostHistory

function template_list_edits()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<h3 class="titlebg">
		<span class="left"></span>
		', $context['ph_topic']['msg_subject'], '
	</h3>
	<table class="table_grid" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th scope="col" class="smalltext first_th">', $txt['ph_last_edit'], '</td>
				<th scope="col" class="smalltext">', $txt['ph_last_time'], '</td>
				<th scope="col" class="smalltext last_th">', $txt['ph_view_edit'], '</td>
			</tr>';
	
	// First we check if moderators have been lazy
	if (empty($context['post_history']))
		echo '
			<tr>
				<th scope="col" class="smalltext first_th">', $txt['ph_last_edit'], '</td>
				<th scope="col" class="smalltext">', $txt['ph_no_edits'], '</td>
				<th scope="col" class="smalltext last_th">', $txt['ph_view_edit'], '</td>
			</tr>
		</thead>';
	else
	{
		echo '
		</thead>
		<tbody>';
			
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
		
		echo '
		</tbody>';
	}
	
	echo '
	</table>';
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
	<body id="help_popup" style="background: white">
		<h3 class="titlebg">
			<span class="left"></span>
			', $context['ph_topic']['msg_subject'], '
		</h3>
		<table class="table_grid" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th scope="col" class="smalltext first_th">', $txt['ph_last_edit'], '</td>
					<th scope="col" class="smalltext">', $txt['ph_last_time'], '</td>
					<th scope="col" class="smalltext last_th">', $txt['ph_view_edit'], '</td>
				</tr>';
	
	// First we check if moderators have been lazy
	if (empty($context['post_history']))
		echo '
				<tr>
					<th scope="col" class="smalltext first_th">', $txt['ph_last_edit'], '</td>
					<th scope="col" class="smalltext">', $txt['ph_no_edits'], '</td>
					<th scope="col" class="smalltext last_th">', $txt['ph_view_edit'], '</td>
				</tr>
			</thead>';
	else
	{
		echo '
			</thead>
			<tbody>';
			
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
		
		echo '
			</tbody>';
	}
	
	echo '
		</table>
		<div style="text-align: center">
			<a href="javascript:self.close();">', $txt['close_window'], '</a>
		</div>
	</body>
</html>';
}

function template_view_edit()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<h3 class="titlebg">
		<span class="left"></span>
		', $context['ph_topic']['msg_subject'], '
	</h3>
	<em>', $txt['ph_last_edit'], ': ', $context['current_edit']['name'], ' (', $context['current_edit']['time'], ')</em><br />
	<div class="windowbg">
		<span class="topslice"><span></span></span>
		<div class="content">
			', $context['current_edit']['body'], '<br />
		</div>
		<span class="botslice"><span></span></span>
	</div>';
}

function template_view_edit_popup()
{
	global $context, $settings, $options, $scripturl, $txt;

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
	<body id="help_popup" style="background: white">
		<h3 class="titlebg">
			<span class="left"></span>
			', $context['ph_topic']['msg_subject'], '
		</h3>
		<em>', $txt['ph_last_edit'], ': ', $context['current_edit']['name'], ' (', $context['current_edit']['time'], ')</em><br />
		<div class="windowbg">
			<span class="topslice"><span></span></span>
			<div class="content">
				', $context['current_edit']['body'], '<br />
				<div style="text-align: center">
					<a href="javascript:self.close();">', $txt['close_window'], '</a>
				</div>
			</div>
			<span class="botslice"><span></span></span>
		</div>		
	</body>
</html>';
}

function template_compare_edit_popup()
{
	global $context, $settings, $options, $scripturl, $txt;

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
	<body id="help_popup" style="background: white">
		<h3 class="titlebg">
			<span class="left"></span>
			', $context['ph_topic']['msg_subject'], '
		</h3>
		<em>', $txt['ph_last_edit'], ': ', $context['current_edit']['name'], ' (', $context['current_edit']['time'], ')</em><br />
		<div class="windowbg">
			<span class="topslice"><span></span></span>
			<div class="content">';
			
	foreach ($context['edit_changes'] as $change)
	{
		if (!is_array($change))
			echo $change;
		else
		{
			if (!empty($change['d']))
				echo '<del style="background-color: #FFDDDD; text-decoration: none">', implode('', $change['d']), '</del>';
			if (!empty($change['i']))
				echo '<ins style="background-color: #DDFFDD; text-decoration: none">', implode('', $change['i']), '</ins>';
		}
	}
				
	echo '
				<br />
				<div style="text-align: center">
					<a href="javascript:self.close();">', $txt['close_window'], '</a>
				</div>
			</div>
			<span class="botslice"><span></span></span>
		</div>		
	</body>
</html>';
}

?>