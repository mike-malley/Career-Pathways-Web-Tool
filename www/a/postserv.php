<?php
chdir("..");
require_once("inc.php");


	/**
	 * AJAX Handler for POST Drawings
	 */
	switch($_GET['mode'])
	{
		case "prompt":
?>
<div class="postGreyboxWrapper">
	<div class="postGreyboxContent">
<?php
			if($_GET['type'] == 'cell')
				printCellForm($_GET['id']);
			elseif($_GET['type'] == 'head')
				printHeadForm($_GET['id']);
			elseif($_GET['type'] == 'footer')
				printFooterForm($_GET['id']);
			else
				die('<div class="greyboxError">Misunderstood Action Type</div>');
?>
	</div>
</div>
<?php
		break;
		case "commit":
			if($_GET['type'] == 'cell')
				commitCell($_GET['id']);
			elseif($_GET['type'] == 'head')
				commitHead($_GET['id']);
			elseif($_GET['type'] == 'footer')
				commitFooter($_GET['id']);
			else
				die('<div class="greyboxError">Misunderstood Commit Type</div>');
		break;
		default:
			echo '<div style="greyboxError">This mode is not supported</div>';
		break;
	}//switch


	/***************************/
	/******* PROMPT MODE *******/
	/***************************/
	function printCellForm($id)
	{
		global $DB;

		$cell = $DB->SingleQuery("SELECT `post_drawing_main`.`type`, `content`, `href`, `course_subject`, `course_number`, `course_title`
			FROM `post_cell`
			LEFT JOIN `post_drawing_main` ON (`post_cell`.`drawing_id` = `post_drawing_main`.`id`)
			WHERE `post_cell`.`id` = '" . intval($id) . "'");

		// Draw the High School form
		ob_start();
		if($cell['type'] == 'HS')
		{
			echo getHSFormHTML($cell);
?>
		<script language="JavaScript" type="text/javascript">
			$("#postFormSave").click(function(){
				$.ajax({
					type: "POST",
					url: "/a/postserv.php?mode=commit&type=cell&id=<?=$id?>",
					data: "content=" + $("#postFormContent").val() + "&href=" + $("#postFormURL").val(),
					success: function(data){
						$("#post_cell_<?=$id?>").html(data);
						chGreybox.close();
					}
				});
			});
		</script>
<?php
		}//if (high school)
		elseif($cell['type'] == 'CC')
		{
			echo getCCFormHTML($cell);
?>
		<script language="JavaScript" type="text/javascript">
			if($("#postTopRadio").attr("checked"))
				$("#postFormContent, #postFormURL").attr("disabled", "disabled");
			else
				$("#postFormSubject, #postFormNumber, #postFormTitle").attr("disabled", "disabled");

			$("#postTopRadio").click(function(){
				$("#postFormSubject, #postFormNumber, #postFormTitle").attr("disabled", false);
				$("#postFormContent, #postFormURL").attr("disabled", "disabled");
			});
			$("#postBottomRadio").click(function(){
				$("#postFormContent, #postFormURL").attr("disabled", false);
				$("#postFormSubject, #postFormNumber, #postFormTitle").attr("disabled", "disabled");
			});

			$("#postFormSave").click(function(){
				if($("#postTopRadio").attr("checked"))
					$("#postFormContent, #postFormURL").val("");
				else
					$("#postFormSubject, #postFormNumber, #postFormTitle").val("");

				$.ajax({
					type: "POST",
					url: "/a/postserv.php?mode=commit&type=cell&id=<?=$id?>",
					data: "subject=" + $("#postFormSubject").val() + "&number=" + $("#postFormNumber").val() + "&title=" + $("#postFormTitle").val() + "&content=" + $("#postFormContent").val() + "&href=" + $("#postFormURL").val(),
					success: function(data){
						$("#post_cell_<?=$id?>").html(data);
						chGreybox.close();
					}
				});
			});
		</script>
<?php
		}//if (community college)

		echo ob_get_clean();
	}//end function printCellForm

	function printHeadForm($id)
	{
		global $DB;
		$cell = $DB->SingleQuery("SELECT `title` FROM `post_col` WHERE `id` = '" . intval($id) . "'");

		ob_start();
?>
		<form action="javascript:void(0);">
			<div style="font-weight: bold;">Header Description:</div>
			<input type="text" id="postFormTitle" style="width: 400px; border: 1px #AAA solid;" value="<?=$cell['title']?>" />
			<br /><br />
			<div style="text-align: right;">
				<input type="button" id="postFormSave" value="Save" style="padding: 3px; background: #E0E0E0; border: 1px #AAA solid; font-weight: bold;" />
			</div>
		</form>
		<script language="JavaScript" type="text/javascript">
			$("#postFormSave").click(function(){
				$.ajax({
					type: "POST",
					url: "/a/postserv.php?mode=commit&type=head&id=<?=$id?>",
					data: "title=" + $("#postFormTitle").val(),
					success: function(data){
						$("#post_header_<?=$id?>").html(data);
						chGreybox.close();
					}
				});
			});
		</script>
<?php
		echo ob_get_clean();
	}//end function printHeadForm

	function printFooterForm( $id)
	{
		global $DB;

		$cell = $DB->SingleQuery("SELECT `footer_text`, `footer_link`
			FROM `post_drawings`
			WHERE `id` = '" . intval($id) . "'");

		// Draw the Footer form
		ob_start();

		echo getFooterHTML($cell);
?>
		<script language="JavaScript" type="text/javascript">
			$("#postFormSave").click(function(){
				$.ajax({
					type: "POST",
					url: "/a/postserv.php?mode=commit&type=footer&id=<?=$id?>",
					data: "text=" + $("#postFormContent").val() + "&link=" + $("#postFormURL").val(),
					success: function(data){
						$("#post_footer_<?=$id?>").html(data);
						chGreybox.close();
					}
				});
			});
		</script>
<?php
	}//end function printFooterForm

	/**************************/
	/****** COMMIT CODE *******/
	/**************************/
	function commitCell($id)
	{
		global $DB;

		// Decide if we are drawing a link or not
		$href = $_POST['href'];
		$link = FALSE;
		if(isset($_POST['href']) && $_POST['href'] != '')
		{
			$link = TRUE;
			if(substr($href, 0, 7) != 'http://' && substr($href, 0, 8) != 'https://')
				$href = 'http://' . $_POST['href'];
		}

		$subject = (isset($_POST['subject']))?$_POST['subject']:'';
		$number = (isset($_POST['number']))?$_POST['number']:'';
		$title = (isset($_POST['title']))?$_POST['title']:'';

		// Update the database
		$DB->Update('post_cell', array('course_subject'=>$subject, 'course_number'=>$number, 'course_title'=>$title, 'content'=>$_POST['content'], 'href' => $href), intval($id));

		// Decide what we should draw back to the page
		if($subject != '' && $number != '' && $title != '')
			echo '<a href="javascript:void(0);">' . $subject . ' ' . $number . '<br />' . $title . '</a>';
		else
			echo ($link?'<a href="javascript:void(0);">':'') . $_POST['content'] . ($link?'</a>':'');
	}//end function commitCell
	
	function commitHead($id)
	{
		global $DB;
		$DB->Update('post_col', array('title' => $_POST['title']), intval($id));
		echo $_POST['title'];
	}//end function commitHeader

	function commitFooter($id)
	{
		global $DB;

		$href = $_POST['link'];
		$link = FALSE;
		if(isset($_POST['link']) && $_POST['link'] != '')
		{
			$link = TRUE;
			if(substr($href, 0, 7) != 'http://' && substr($href, 0, 8) != 'https://')
				$href = 'http://' . $_POST['link'];
		}

		$DB->Update('post_drawings', array('footer_text' => $_POST['text'], 'footer_link'=>$href), intval($id));
		echo ($link?'<a href="javascript:void(0);">':'') . $_POST['text'] . ($link?'</a>':'');
	}//end function commitFooter

	/*****************************/
	/******* FORM PRINTERS *******/
	/*****************************/

	function getHSFormHTML(&$cell = NULL)
	{
		if(!$cell)
			$cell = array('content'=>'', 'href'=>'');

		ob_start();
?>
		<form action="javascript:void(0);">
			<div style="font-weight: bold;">Course Content:</div>
			<input type="text" id="postFormContent" style="width: 400px; border: 1px #AAA solid;" value="<?=$cell['content']?>" />
			<br /><br />
			<div style="font-weight: bold;">Link this Content: <span style="color: #777777; font-size: 10px; font-weight: normal;">(Optional)</span></div>
			URL: <input type="text" id="postFormURL" style="width: 300px; border: 1px #AAA solid;" value="<?=$cell['href']?>" />
			<br /><br />
			<div style="text-align: right;">
				<input type="button" id="postFormSave" value="Save" style="padding: 3px; background: #E0E0E0; border: 1px #AAA solid; font-weight: bold;" />
			</div>
		</form>
<?php
		return ob_get_clean();
	}//end function printHSFormHTML

	function getCCFormHTML(&$cell = NULL)
	{
		ob_start();
?>
		<form action="javascript:void(0);">
			<table border="0" cellpadding="0" cellspacing="0" style="width: 100%; height: 100%">
				<tr>
					<td valign="top">
						<input type="radio" name="postModeSelector" id="postTopRadio"<?=(($cell['course_subject'] != '' || !$cell['content'])?' checked="checked"':'')?> />
					</td>
					<td id="postTopHalf" style="padding-left: 20px;" valign="top">
						<div style="float: left; width: 150px; height: 20px; font-weight: bold;">Course Subject:</div>
						<div style="float: left; width: 50px; height: 20px;">
							<input id="postFormSubject" maxlength="4" value="<?=$cell['course_subject']?>" />
						</div>
						<div style="clear: both; float: left; width: 150px; height: 20px; font-weight: bold;">Course Number:</div>
						<div style="float: left; width: 50px; height: 20px;">
							<input id="postFormNumber" maxlength="4" value="<?=$cell['course_number']?>" />
						</div>
						<div style="clear: both; font-weight: bold;">Course Title:</div>
							<input id="postFormTitle" maxlength="255" style="width: 340px;" value="<?=$cell['course_title']?>" />
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<div style="clear: both; height: 10px;"></div>
						<div style="width: 100%; font-weight: bold; text-align: center;">&mdash;&mdash;&mdash;&mdash;&mdash; OR &mdash;&mdash;&mdash;&mdash;&mdash;</div>
						<div style="clear: both; height: 10px;"></div>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<input type="radio" name="postModeSelector" id="postBottomRadio"<?=(($cell['course_subject'] == '' && $cell['content'])?' checked="checked"':'')?> />
					</td>
					<td id="postBottomHalf" valign="top" style="padding-left: 20px;">
						<div style="font-weight: bold;">Course Content:</div>
						<input type="text" id="postFormContent" style="width: 340px; border: 1px #AAA solid;" value="<?=$cell['content']?>" />
						<br /><br />
						<div style="font-weight: bold;">Link this Content: <span style="color: #777777; font-size: 10px; font-weight: normal;">(Optional)</span></div>
						URL: <input type="text" id="postFormURL" style="width: 300px; border: 1px #AAA solid;" value="<?=$cell['href']?>" />
					</td>
				</tr>
			</table>
			<br />
			<div style="text-align: right;">
				<input type="button" id="postFormSave" value="Save" style="padding: 3px; background: #E0E0E0; border: 1px #AAA solid; font-weight: bold;" />
			</div>
		</form>
<?php
		return ob_get_clean();	
	}//end function printCCFormHTML

	function getFooterHTML(&$footer = NULL)
	{
		if(!$footer)
			$footer = array('text'=>'', 'link'=>'');

		ob_start();
?>
		<form action="javascript:void(0);">
			<div style="font-weight: bold;">Footer Content:</div>
			<input type="text" id="postFormContent" style="width: 400px; border: 1px #AAA solid;" value="<?=$footer['footer_text']?>" />
			<br /><br />
			<div style="font-weight: bold;">Link this Content: <span style="color: #777777; font-size: 10px; font-weight: normal;">(Optional)</span></div>
			URL: <input type="text" id="postFormURL" style="width: 300px; border: 1px #AAA solid;" value="<?=$footer['footer_link']?>" />
			<br /><br />
			<div style="text-align: right;">
				<input type="button" id="postFormSave" value="Save" style="padding: 3px; background: #E0E0E0; border: 1px #AAA solid; font-weight: bold;" />
			</div>
		</form>
<?php
		return ob_get_clean();
	}//end function getFooterHTML
?>