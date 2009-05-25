<?php
// Version: 0.1; PostHistory

function template_main()
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
				<td>', $edit['time'], '</td>
				<td><a href="', $edit['href'], '">', $txt['ph_view_edit'], '</a></td>
			</tr>';
				
			$alternate = !$alternate;
		}
	}
	
	echo '
		</table>
	</div>';
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

?>