<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 5               */
/* CACHE FILE: Generated: Thu, 26 Nov 2015 13:45:40 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_emails_5 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['forward_form'] = array('language','lang','hasError','hasSubject','hasText','hasCaptcha');


}

/* -- boardRules --*/
function boardRules($title="",$body="") {
$IPBHTML = "";
$IPBHTML .= "" . $this->registry->getClass('output')->getReplacement("header_start") . "<h3 class='maintitle'>{$title}</h3>" . $this->registry->getClass('output')->getReplacement("header_end") . "
<div class='row2 ipsPad rules'>
	{$body}
</div>
" . $this->registry->getClass('output')->getReplacement("box_end") . "
<br />";
return $IPBHTML;
}

/* -- forward_form --*/
function forward_form($title="",$text="",$lang="", $captchaHTML='', $msg='') {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_emails', $this->_funcHooks['forward_form'] ) )
{
$count_0caf814fefb71bf6c31776747473619c = is_array($this->functionData['forward_form']) ? count($this->functionData['forward_form']) : 0;
$this->functionData['forward_form'][$count_0caf814fefb71bf6c31776747473619c]['title'] = $title;
$this->functionData['forward_form'][$count_0caf814fefb71bf6c31776747473619c]['text'] = $text;
$this->functionData['forward_form'][$count_0caf814fefb71bf6c31776747473619c]['lang'] = $lang;
$this->functionData['forward_form'][$count_0caf814fefb71bf6c31776747473619c]['captchaHTML'] = $captchaHTML;
$this->functionData['forward_form'][$count_0caf814fefb71bf6c31776747473619c]['msg'] = $msg;
}
$IPBHTML .= "<form action=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=forums&amp;module=extras&amp;section=forward", "public",'' ), "", "" ) . "\" method=\"post\" name='REPLIER'>
	<input type=\"hidden\" name=\"do\" value=\"01\" />
	<input type=\"hidden\" name=\"st\" value=\"{$this->request['st']}\" />
	<input type=\"hidden\" name=\"f\" value=\"{$this->request['f']}\" />
	<input type=\"hidden\" name=\"t\" value=\"{$this->request['t']}\" />
	<input type=\"hidden\" name=\"url\" value=\"{$this->request['url']}\" />
	<input type=\"hidden\" name=\"title\" value=\"{$this->request['title']}\" />
	<input type='hidden' name='k' value='{$this->member->form_hash}' />
	" . (($msg) ? ("
		<p class='message error'>{$this->lang->words[ $msg ]}</p><br />
	") : ("
		<p class='message'>{$this->lang->words['email_friend']}</p><br />
	")) . "
	
	" . $this->registry->getClass('output')->getReplacement("header_start") . "<h2 class='maintitle'>{$this->lang->words['title']}</h2>" . $this->registry->getClass('output')->getReplacement("header_end") . "
	<div class='generic_bar'></div>
	<div class='ipsForm ipsForm_horizontal'>
		<fieldset>
			<h3 class='bar'>{$this->lang->words['email_recepient']}</h3>
			<ul class='ipsPad'>
				" . ((count($this->caches['lang_data']) == 1) ? ("
					<input type='hidden' name='lang' value='{$this->caches['lang_data'][0]['lang_id']}' />
				") : ("
					<li class='ipsField clear'>
						<label for='to_lang' class='ipsField_title'>{$this->lang->words['send_lang']}</label>
						<p class='ipsField_content'>
							<select name='lang' class='input_select' id='to_lang'>
								".$this->__f__bd5e696ae5da4df932f5b0161a89b45f($title,$text,$lang,$captchaHTML,$msg)."							</select>
						</p>
					</li>
				")) . "
				<li class='ipsField clear'>
					<label for='to_name' class='ipsField_title'>{$this->lang->words['to_name']}</label>
					<p class='ipsField_content'>
						<input type=\"text\" id='to_name' class='input_text' name=\"to_name\" value=\"{$this->request['to_name']}\" size=\"30\" maxlength=\"100\" />
					</p>
				</li>
				<li class='ipsField clear'>
					<label for='to_email' class='ipsField_title'>{$this->lang->words['to_email']}</label>
					<p class='ipsField_content'>
						<input type=\"text\" id='to_email' class='input_text' name=\"to_email\" value=\"{$this->request['to_email']}\" size=\"30\" maxlength=\"100\" />
					</p>
				</li>
				<li class='ipsField clear'> 
					<label for='subject' class='ipsField_title'>{$this->lang->words['subject']}</label>
					<p class='ipsField_content'>
						<input type=\"text\" id=\"subject\" class=\"input_text\" name=\"subject\" value=\"" . (($this->request['subject']) ? ("{$this->request['subject']}") : ("$title")) . "\" size=\"30\" maxlength=\"120\" />
					</p>
				</li> 				
				<li class='ipsField clear'>
					<label for='to_message' class='ipsField_title'>{$this->lang->words['message']}</label>
					<p class='ipsField_content'>
						<textarea id='to_message' cols=\"60\" rows=\"12\" wrap=\"soft\" name=\"message\" class=\"input_text\">" . (($this->request['message']) ? ("" . IPSText::br2nl($this->request['message']) . "") : ("$text")) . "</textarea>
					</p>
				</li>
				" . (($captchaHTML) ? ("
					<li class='ipsField clear'>
						{$captchaHTML}
					</li>
				") : ("")) . "
			</ul>
		</fieldset>
		<fieldset class='submit'>
			<input class='input_submit' type=\"submit\" value=\"{$this->lang->words['submit_send']}\" />
		</fieldset>
	</div>" . $this->registry->getClass('output')->getReplacement("box_end") . "
</form>";
return $IPBHTML;
}


function __f__bd5e696ae5da4df932f5b0161a89b45f($title="",$text="",$lang="", $captchaHTML='', $msg='')
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $this->caches['lang_data'] as $l )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
									<option value='{$l['lang_id']}' " . (($l['lant_id'] == $this->memberData['language']) ? ("selected='selected'") : ("")) . ">{$l['lang_title']}</option>
								
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>