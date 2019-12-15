<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Sun, 24 May 2015 13:55:24 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_topic_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['ajaxSharePost'] = array('shareLinksStrip','shareLinksJS');
$this->_funcHooks['announcement_show'] = array('canmanage','disablelightbox');
$this->_funcHooks['archiveStatusMessage'] = array('isAdminArchivedTop','isRestoring','isWorking','isModArchived','isAdminArchived','isArchivedDone');
$this->_funcHooks['hookFacebookLike'] = array('checkAccess','fbAppIdPresent');
$this->_funcHooks['likeSummaryContents'] = array('likeOnlyMembers');
$this->_funcHooks['pollDisplay'] = array('lastVoter','poll_voters','hasVoters','viewVoters','votersJs','multiVote','showingResults','poll_choices','votedClass','noGuestVote','poll_questions','showPollResults','publicPollNotice','deleteVote','cast','viewVotersLink','alreadyDisplayVotes','displayVotes','youCreatedPoll','voteButtonVoted','voteButtonMid','voteButton','editPoll');
$this->_funcHooks['post'] = array('sDeleted','isSolvedCss','postQueued','noRep','posRep','negRep','repIgnored','userIgnoredLang','userIgnoredLangTwo','userIgnored','isNotIgnoring','postMid','hasPages','postModSelected','postModCheckbox','postMember','accessModCP','postAdmin','postIp','isSolvedSausage','repHighlight','postEditByReason','postEditBy','repButtonsLike','postSignature','canDelete','approveUnapprove','canEdit','controlsForUnapprovedPost','hideNormalControlsForUnapprovedPost','canUnanswer','notAnswered','isAnswered','canAnswerTopic','multiquote','replyButton','replyButtonWarn','canDelete','canHide','canEdit','postIsReported','canReportPost','reportedPostData','sDeletedNot','initIgnoredPost','adCodeCheck','sDeletedNotMQ','repButtonsLike','canReportPost','canEdit','canDelete','replyButton','canHide');
$this->_funcHooks['quickEditPost'] = array('editReasonQe','editByQe');
$this->_funcHooks['show_attachment_title'] = array('attachType','attach','attachType','attach');
$this->_funcHooks['Show_attachments'] = array('hasmime','hasmime');
$this->_funcHooks['softDeletedPostBit'] = array('postMid','postModSelected','postModCheckbox','postMember','postAdmin','postIp','showReason','postEditByReason','postEditBy','postSignature','showPost','sdOptions');
$this->_funcHooks['topicViewTemplate'] = array('isDelete','mod_links','mm','optRepFilterSelected','reputation_levels','post_data','topics','allowRating','canDeleteUrls','disablelightbox','followsismember','rate1','rate2','rate3','rate4','rate5','hasRates','jsHasRates','topicRating','hasTags','canSeeProfiles1','canSeeProfiles2','hasPagesBA','samePageBA','hasPagesBA2','samePageBA2','bestAnswer','showReason','tbdSoftRestore','tbdRestore','topicHasBeenHidden','topicHasBeenDeleted','closedButtonLink','closedButtonLink','pollOnly','isMemberTop','replyButtonLink','replyButton','closedButton','topicDescription','modOptions','isArchivedPostBox','mmModOptions','modOptionsDropdown','repFilterDefault','reputationFilter','optSelectStar','repFilterOptions','reputationFilter','hasPosts','modOptions2','hasUnreadNext','isLockedFR','isMember','loadJsManually','fastReply','canShare','sameTagged','auNames','topicActiveUsers','scrollToPost','post_data','closedButtonLink');


}

/* -- ajax__deletePost --*/
function ajax__deletePost() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- ajax__doDeletePost --*/
function ajax__doDeletePost() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- ajax__restoreTopicDialog --*/
function ajax__restoreTopicDialog() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- ajaxSharePost --*/
function ajaxSharePost($topic, $post, $url, $sharelinks) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['ajaxSharePost'] ) )
{
$count_d6b9a0d5813aea6e47d29612d80a03de = is_array($this->functionData['ajaxSharePost']) ? count($this->functionData['ajaxSharePost']) : 0;
$this->functionData['ajaxSharePost'][$count_d6b9a0d5813aea6e47d29612d80a03de]['topic'] = $topic;
$this->functionData['ajaxSharePost'][$count_d6b9a0d5813aea6e47d29612d80a03de]['post'] = $post;
$this->functionData['ajaxSharePost'][$count_d6b9a0d5813aea6e47d29612d80a03de]['url'] = $url;
$this->functionData['ajaxSharePost'][$count_d6b9a0d5813aea6e47d29612d80a03de]['sharelinks'] = $sharelinks;
}
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- ajaxSigCloseMenu --*/
function ajaxSigCloseMenu($post) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- announcement_show --*/
function announcement_show($announce="",$author="") {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['announcement_show'] ) )
{
$count_a18b4990c8e0b8e3e3d6208def273c3a = is_array($this->functionData['announcement_show']) ? count($this->functionData['announcement_show']) : 0;
$this->functionData['announcement_show'][$count_a18b4990c8e0b8e3e3d6208def273c3a]['announce'] = $announce;
$this->functionData['announcement_show'][$count_a18b4990c8e0b8e3e3d6208def273c3a]['author'] = $author;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- archiveStatusMessage --*/
function archiveStatusMessage($topic, $forum) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['archiveStatusMessage'] ) )
{
$count_435775de1fab96f913a795191a5728c3 = is_array($this->functionData['archiveStatusMessage']) ? count($this->functionData['archiveStatusMessage']) : 0;
$this->functionData['archiveStatusMessage'][$count_435775de1fab96f913a795191a5728c3]['topic'] = $topic;
$this->functionData['archiveStatusMessage'][$count_435775de1fab96f913a795191a5728c3]['forum'] = $forum;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- hookFacebookLike --*/
function hookFacebookLike() {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['hookFacebookLike'] ) )
{
$count_1bc180a24b6a81d2f88880d2c8c8f688 = is_array($this->functionData['hookFacebookLike']) ? count($this->functionData['hookFacebookLike']) : 0;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- likeSummary --*/
function likeSummary($data, $relId, $opts) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- likeSummaryContents --*/
function likeSummaryContents($data, $relId, $opts=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['likeSummaryContents'] ) )
{
$count_f5c36434c4fd06d27835fe560ae52c14 = is_array($this->functionData['likeSummaryContents']) ? count($this->functionData['likeSummaryContents']) : 0;
$this->functionData['likeSummaryContents'][$count_f5c36434c4fd06d27835fe560ae52c14]['data'] = $data;
$this->functionData['likeSummaryContents'][$count_f5c36434c4fd06d27835fe560ae52c14]['relId'] = $relId;
$this->functionData['likeSummaryContents'][$count_f5c36434c4fd06d27835fe560ae52c14]['opts'] = $opts;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- pollDisplay --*/
function pollDisplay($poll, $topicData, $forumData, $pollData, $showResults, $editPoll=1) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['pollDisplay'] ) )
{
$count_de80b643ad8c72416b464013afa31a24 = is_array($this->functionData['pollDisplay']) ? count($this->functionData['pollDisplay']) : 0;
$this->functionData['pollDisplay'][$count_de80b643ad8c72416b464013afa31a24]['poll'] = $poll;
$this->functionData['pollDisplay'][$count_de80b643ad8c72416b464013afa31a24]['topicData'] = $topicData;
$this->functionData['pollDisplay'][$count_de80b643ad8c72416b464013afa31a24]['forumData'] = $forumData;
$this->functionData['pollDisplay'][$count_de80b643ad8c72416b464013afa31a24]['pollData'] = $pollData;
$this->functionData['pollDisplay'][$count_de80b643ad8c72416b464013afa31a24]['showResults'] = $showResults;
$this->functionData['pollDisplay'][$count_de80b643ad8c72416b464013afa31a24]['editPoll'] = $editPoll;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- post --*/
function post($post, $displayData, $topic, $forum=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['post'] ) )
{
$count_610c025ad0d16e5e2a26e5f53d66768f = is_array($this->functionData['post']) ? count($this->functionData['post']) : 0;
$this->functionData['post'][$count_610c025ad0d16e5e2a26e5f53d66768f]['post'] = $post;
$this->functionData['post'][$count_610c025ad0d16e5e2a26e5f53d66768f]['displayData'] = $displayData;
$this->functionData['post'][$count_610c025ad0d16e5e2a26e5f53d66768f]['topic'] = $topic;
$this->functionData['post'][$count_610c025ad0d16e5e2a26e5f53d66768f]['forum'] = $forum;
}
$IPBHTML .= "<post>
					<id>{$post['post']['pid']}</id>
					<date>" . IPSText::htmlspecialchars($this->registry->getClass('class_localization')->getDate($post['post']['post_date'],"DATE", 0)) . "</date>
					<url>" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showtopic={$post['post']['topic_id']}&amp;view=findpost&amp;p={$post['post']['pid']}", "public",'' ), "{$topic['title_seo']}", "showtopic" ) . "</url>
					<text><![CDATA[
									{$post['post']['post']}{$post['post']['attachmentHtml']}
						]]>	
					</text>
					<reputation>{$post['post']['rep_points']}</reputation>
					<user>
		" . (($post['author']['member_id']) ? ("
						<id>{$post['author']['member_id']}</id>
						<name><![CDATA[{$post['author']['members_display_name']}]]></name>
						<avatar><![CDATA[{$post['author']['pp_thumb_photo']}]]></avatar>
						<url><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$post['author']['member_id']}", "public",'' ), "{$post['author']['members_seo_name']}", "showuser" ) . "]]></url>
						<online>{$post['author']['_online']}</online>
						" . ( method_exists( $this->registry->getClass('output')->getTemplate('global'), 'userInfoPane' ) ? $this->registry->getClass('output')->getTemplate('global')->userInfoPane($post['author'], $post['post']['pid'], array()) : '' ) . "") : ("
						<id>0</id>
						<name><![CDATA[{$post['author']['members_display_name']}]]></name>
		")) . "
					</user>
		<options>
		" . ((! $topic['_isArchived']) ? ("
				" . ( method_exists( $this->registry->getClass('output')->getTemplate('global_other'), 'repButtons' ) ? $this->registry->getClass('output')->getTemplate('global_other')->repButtons($post['author'], array_merge( array( 'primaryId' => $post['post']['pid'], 'domLikeStripId' => 'like_post_' . $post['post']['pid'], 'domCountId' => 'rep_post_' . $post['post']['pid'], 'app' => 'forums', 'type' => 'pid', 'likeFormatted' => $post['post']['like']['formatted'] ), $post['post'] )) : '' ) . "
				") : ("")) . "				
			" . (($topic['_canReport'] and ( $this->memberData['member_id'] ) && ! $topic['_isArchived']) ? ("
			<reportURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=core&amp;module=reports&amp;rcom=post&amp;tid={$this->request['t']}&amp;fid={$this->request['f']}&amp;pid={$post['post']['pid']}&amp;st={$this->request['st']}", "public",'' ), "", "" ) . "]]></reportURL>
			") : ("")) . "
			" . (($post['post']['_can_edit'] === TRUE) ? ("
				<editURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=post&amp;section=post&amp;do=edit_post&amp;f={$this->request['f']}&amp;t={$this->request['t']}&amp;p={$post['post']['pid']}&amp;st={$this->request['st']}", "publicWithApp",'' ), "", "" ) . "]]></editURL>
			") : ("")) . "
			
			" . (($post['post']['_can_delete'] === TRUE && ! $topic['_isArchived']) ? ("
				<deleteURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=moderate&amp;section=moderate&amp;do=postchoice&amp;tact=deletedo&amp;f={$topic['forum_id']}&amp;t={$topic['tid']}&amp;selectedpids[]={$post['post']['pid']}&amp;st={$this->request['st']}&amp;auth_key={$this->member->form_hash}", "publicWithApp",'' ), "", "" ) . "]]></deleteURL>
			") : ("")) . "
			" . (($post['post']['_canReply']) ? ("
				<replyURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=post&amp;section=post&amp;do=reply_post&amp;f={$this->request['f']}&amp;t={$this->request['t']}&amp;qpid={$post['post']['pid']}", "publicWithApp",'' ), "", "" ) . "]]></replyURL>
			") : ("")) . "
			" . (($post['post']['_softDelete'] && ! $topic['_isArchived']) ? ("
				<hideURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=moderate&amp;section=moderate&amp;do=postchoice&amp;tact=delete&amp;f={$topic['forum_id']}&amp;t={$topic['tid']}&amp;selectedpids[]={$post['post']['pid']}&amp;st={$this->request['st']}&amp;auth_key={$this->member->form_hash}", "publicWithApp",'' ), "", "" ) . "]]></hideURL>
			") : ("")) . "
			
		</options>
					
				</post>";
return $IPBHTML;
}

/* -- quickEditPost --*/
function quickEditPost($post) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['quickEditPost'] ) )
{
$count_5a214fd0e5b0ccc74c37165ec6aec931 = is_array($this->functionData['quickEditPost']) ? count($this->functionData['quickEditPost']) : 0;
$this->functionData['quickEditPost'][$count_5a214fd0e5b0ccc74c37165ec6aec931]['post'] = $post;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- show_attachment_title --*/
function show_attachment_title($title="",$data="",$type="") {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['show_attachment_title'] ) )
{
$count_a13542dbc608bd10dd1a6d20ba37a252 = is_array($this->functionData['show_attachment_title']) ? count($this->functionData['show_attachment_title']) : 0;
$this->functionData['show_attachment_title'][$count_a13542dbc608bd10dd1a6d20ba37a252]['title'] = $title;
$this->functionData['show_attachment_title'][$count_a13542dbc608bd10dd1a6d20ba37a252]['data'] = $data;
$this->functionData['show_attachment_title'][$count_a13542dbc608bd10dd1a6d20ba37a252]['type'] = $type;
}
$IPBHTML .= "<div id='attach_wrap' class='clearfix'>
	<h4>{$title}</h4>
	<ul>
		".$this->__f__4ac745c0152e20a01c978dde850308ea($title,$data,$type)."	</ul>
</div>";
return $IPBHTML;
}


function __f__4ac745c0152e20a01c978dde850308ea($title="",$data="",$type="")
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $data as $file )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
			<li class='" . (($type == 'attach') ? ("attachment") : ("")) . "'>
				{$file}
			</li>
		
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- Show_attachments --*/
function Show_attachments($data="") {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['Show_attachments'] ) )
{
$count_4bb58569631be39de67ce3621784e0a9 = is_array($this->functionData['Show_attachments']) ? count($this->functionData['Show_attachments']) : 0;
$this->functionData['Show_attachments'][$count_4bb58569631be39de67ce3621784e0a9]['data'] = $data;
}
$IPBHTML .= "<a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=core&amp;module=attach&amp;section=attach&amp;attach_id={$data['attach_id']}", "public",'' ), "", "" ) . "\" title=\"{$this->lang->words['attach_dl']}\"><img src=\"{$this->settings['public_dir']}" . (($data['mime_image']) ? ("{$data['mime_image']}") : ("style_extra/mime_types/unknown.gif")) . "\" alt=\"{$this->lang->words['attached_file']}\" /></a>
&nbsp;<a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=core&amp;module=attach&amp;section=attach&amp;attach_id={$data['attach_id']}", "public",'' ), "", "" ) . "\" title=\"{$this->lang->words['attach_dl']}\"><strong>{$data['attach_file']}</strong></a> &nbsp;&nbsp;<span class='desc'><strong>{$data['file_size']}</strong></span>
&nbsp;&nbsp;<span class=\"desc lighter\">{$data['attach_hits']} {$this->lang->words['attach_hits']}</span>";
return $IPBHTML;
}

/* -- Show_attachments_img --*/
function Show_attachments_img($data=array()) {
$IPBHTML = "";
$IPBHTML .= "<a class='resized_img' rel='lightbox[{$data['attach_rel_id']}]' id='ipb-attach-url-{$data['_attach_id']}' href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=core&amp;module=attach&amp;section=attach&amp;attach_rel_module={$data['type']}&amp;attach_id={$data['attach_id']}", "public",'' ), "", "" ) . "\" title=\"{$data['location']} - {$this->lang->words['attach_size']} {$data['file_size']}, {$this->lang->words['attach_ahits']} {$data['attach_hits']}\"><img itemprop=\"image\" src=\"{$this->settings['upload_url']}/{$data['o_location']}\" class='bbc_img linked-image' alt=\"{$this->lang->words['pic_attach']}: {$data['location']}\" /></a>
" . $this->registry->output->addMetaTag( 'og:image', "{$this->settings['upload_url']}/{$data['t_location']}", false ) . "";
return $IPBHTML;
}

/* -- Show_attachments_img_thumb --*/
function Show_attachments_img_thumb($data=array()) {
$IPBHTML = "";
$IPBHTML .= "<a class='resized_img' rel='lightbox[{$data['attach_rel_id']}]' id='ipb-attach-url-{$data['_attach_id']}' href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=core&amp;module=attach&amp;section=attach&amp;attach_rel_module={$data['type']}&amp;attach_id={$data['attach_id']}", "public",'' ), "", "" ) . "\" title=\"{$data['location']} - {$this->lang->words['attach_size']} {$data['file_size']}, {$this->lang->words['attach_ahits']} {$data['attach_hits']}\"><img itemprop=\"image\" src=\"{$this->settings['upload_url']}/{$data['t_location']}\" id='ipb-attach-img-{$data['_attach_id']}' style='width:{$data['t_width']};height:{$data['t_height']}' class='attach' width=\"{$data['t_width']}\" height=\"{$data['t_height']}\" alt=\"{$this->lang->words['pic_attach']}: {$data['location']}\" /></a>
" . $this->registry->output->addMetaTag( 'og:image', "{$this->settings['upload_url']}/{$data['t_location']}", false ) . "";
return $IPBHTML;
}

/* -- softDeletedPostBit --*/
function softDeletedPostBit($post, $sdData, $topic) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['softDeletedPostBit'] ) )
{
$count_6c4823af8c0675571ef784c8f0932be0 = is_array($this->functionData['softDeletedPostBit']) ? count($this->functionData['softDeletedPostBit']) : 0;
$this->functionData['softDeletedPostBit'][$count_6c4823af8c0675571ef784c8f0932be0]['post'] = $post;
$this->functionData['softDeletedPostBit'][$count_6c4823af8c0675571ef784c8f0932be0]['sdData'] = $sdData;
$this->functionData['softDeletedPostBit'][$count_6c4823af8c0675571ef784c8f0932be0]['topic'] = $topic;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- topicPreview --*/
function topicPreview($topic, $posts) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- topicViewTemplate --*/
function topicViewTemplate($forum, $topic, $post_data, $displayData) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_topic', $this->_funcHooks['topicViewTemplate'] ) )
{
$count_574b1de2e201cbe4c0b60bf4d15c1e9b = is_array($this->functionData['topicViewTemplate']) ? count($this->functionData['topicViewTemplate']) : 0;
$this->functionData['topicViewTemplate'][$count_574b1de2e201cbe4c0b60bf4d15c1e9b]['forum'] = $forum;
$this->functionData['topicViewTemplate'][$count_574b1de2e201cbe4c0b60bf4d15c1e9b]['topic'] = $topic;
$this->functionData['topicViewTemplate'][$count_574b1de2e201cbe4c0b60bf4d15c1e9b]['post_data'] = $post_data;
$this->functionData['topicViewTemplate'][$count_574b1de2e201cbe4c0b60bf4d15c1e9b]['displayData'] = $displayData;
}
$IPBHTML .= "" . (($displayData['reply_button']['url']) ? ("
	<AssessoryButtonURL><![CDATA[{$displayData['reply_button']['url']}]]></AssessoryButtonURL>
") : ("")) . "

<template>viewTopic</template>
<pagination>{$topic['SHOW_PAGES']}</pagination>
<forum>
		<name><![CDATA[{$forum['name']}]]></name>
		<id>{$forum['id']}</id>
		<url><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showforum={$forum['id']}", "public",'' ), "{{$forum['name_seo']}", "showforum" ) . "]]></url>
		
" . (($forum['show_rules'] == 2) ? ("
		<rules>
			<title>{$forum['rules_title']}</title>
			<text>
				<![CDATA[
						{$forum['rules_text']}
				]]>	
			</text>
		</rules>
") : ("")) . "
" . (($forum_data['show_rules'] == 1) ? ("
		<rules>
			<title><![CDATA[{$forum['rules_title']}]]></title>
			<url><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showforum={$forum['id']}&amp;act=SR", "public",'' ), "", "" ) . "]]></url>
		</rules>
") : ("")) . "
	
		<topic>
			<id>{$topic['tid']}</id>
			<title><![CDATA[{$topic['title']}]]></title>
			<description><![CDATA[{$topic['description']}]]></description>
			<url><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showtopic={$topic['tid']}", "public",'' ), "{$topic['title_seo']}", "showtopic" ) . "]]></url>			
			<posts>				
" . ((is_array( $post_data ) AND count( $post_data )) ? ("".$this->__f__3b2e9bd7a161b932575bdd5b7dbdb0d6($forum,$topic,$post_data,$displayData)."") : ("")) . "
			</posts>
		</topic>
	</forum>";
return $IPBHTML;
}


function __f__3b2e9bd7a161b932575bdd5b7dbdb0d6($forum, $topic, $post_data, $displayData)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $post_data as $pid => $post )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
						" . ( method_exists( $this->registry->getClass('output')->getTemplate('topic'), 'post' ) ? $this->registry->getClass('output')->getTemplate('topic')->post($post, $displayData, $topic, $forum) : '' ) . "
					
	
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