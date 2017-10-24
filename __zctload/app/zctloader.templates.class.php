<?php
class ExampleAppTemplates
{
	public static function NoMsgWrap($str)
	{
		return <<<TPL
<div style="text-align:center;font-weight:bold;width:100%;">
	$str
</div>
TPL;
	}
	public static function MsgListWrap($ml)
	{
		return <<<TPL
<table style="width:100%;">
	$ml
</table>
TPL;
	}
	public static function MsgWrap($msg)
	{
		$date = date('d M Y, H:i:s',$msg['msg_time']);
		return <<<TPL
	<tr>
		<td style="width:15%;">
			$date
		</td>
		<td style="width:85%;">
			{$msg['msg_author']} ({$msg['msg_ip']} :: {$msg['msg_ua']})
		</td>
	</tr>
	<tr>
		<td colspan="2">
			{$msg['msg_data']}
		</td>
	</tr>
TPL;
	}
	public static function NavWrap($nav)
	{
		return <<<TPL
	<tr>
		<td colspan="2" style="text-align:center;">
			$nav
		</td>
	</tr>
TPL;
	}
	public static function Sep()
	{
		return <<<TPL
	<tr>
		<td colspan="2" style="width:100%;height:10px;">
			<hr />
		</td>
	</tr>
TPL;
	}
}
?>