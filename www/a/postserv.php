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
			elseif($_GET['type'] == 'swap')
				die('<div class="greyboxError">Cannot Prompt to Swap, must Commit to Swap</div>');
			else
				die('<div class="greyboxError">Misunderstood Prompting</div>');
?>
	</div>
</div>
<?php
		break;
		case "commit":

			switch( $_GET['type'] )
			{
				case 'cell':
					$version_id = $DB->GetValue('drawing_id', 'post_cell', intval($_GET['id']));
					break;
				case 'head':
					$version_id = $DB->GetValue('drawing_id', 'post_col', intval($_GET['id']));
					break;
				case 'footer':
					$version_id = intval($_GET['id']);
					break;
				case 'swap':
					$version_id = $DB->GetValue('drawing_id', 'post_cell', intval($_POST['toID']));
					break;
				default:
					die('<div class="greyboxError">Misunderstood Commit Type</div>');
			}

			if( !CanEditVersion($version_id) )
			{
				die('Cannot edit this drawing due to a permissions error');
			}
			$drawing = $DB->SingleQuery("SELECT * FROM post_drawings WHERE id=".$version_id);

			$update = array();
			$update['last_modified'] = $DB->SQLDate();
			$update['last_modified_by'] = $_SESSION['user_id'];
			$DB->Update('post_drawings', $update, $version_id);
			$DB->Update('post_drawing_main', $update, $drawing['parent_id']);
			
			switch( $_GET['type'] )
			{
				case 'cell':
					commitCell($_GET['id']);
					break;
				case 'head':
					commitHead($_GET['id']);
					break;
				case 'footer':
					commitFooter($_GET['id']);
					break;
				case 'swap':
					commitSwap($_POST['fromID'], $_POST['toID']);
					break;
			}

		break;
		default:
			echo '<div style="greyboxError">You can only Prompt or Commit - Nothing Else.</div>';
		break;
	}//switch


	/***************************/
	/******* PROMPT MODE *******/
	/***************************/
	function printCellForm($id)
	{
		global $DB;

		$cell = $DB->SingleQuery("SELECT `post_drawing_main`.`type`, `content`, `href`, `legend`, `course_subject`, `course_number`, `course_title`
			FROM `post_cell`
			LEFT JOIN `post_drawings` ON (`post_cell`.`drawing_id` = `post_drawings`.`id`)
			LEFT JOIN `post_drawing_main` ON (`post_drawings`.`parent_id` = `post_drawing_main`.`id`)
			WHERE `post_cell`.`id` = '" . intval($id) . "'");

		// Draw the High School form
		ob_start();
		if($cell['type'] == 'HS')
		{
			echo getHSFormHTML($cell);
?>
		<script language="JavaScript" type="text/javascript">
			$("#postFormContent").focus();

			$(".postGreyboxContent input").keydown(function(e) {
				if( e.keyCode == 13 ) $("#postFormSave").click();
			});

			$("#postFormSave").click(function(){
				var legendData = ''
				$.each($(".post_legend_input"), function() {
					var id = $(this).attr("id").split("_");
					id = id[2];
					legendData += '&legend[' + id + ']=' + $(this).val()
				});
				$.ajax({
					type: "POST",
					url: "/a/postserv.php?mode=commit&type=cell&id=<?=$id?>",
					data: "content=" + $("#postFormContent").val() + "&href=" + $("#postFormURL").val() + legendData,
					success: function(data){
						$("#post_cell_<?=$id?>").html(data);
						var bgSwap = $("#post_cell_<?=$id?>").children().css("background");
						$("#post_cell_<?=$id?>").parent().css({"background" : bgSwap});
						$("#post_cell_<?=$id?>").children().css({"background" : "none"});
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
			$("#postFormSubject").focus();

			$(".postGreyboxContent input").keydown(function(e) {
				if( e.keyCode == 13 ) $("#postFormSave").click();
			});

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

				var legendData = ''
				$.each($(".post_legend_input"), function() {
					var id = $(this).attr("id").split("_");
					id = id[2];
					legendData += '&legend[' + id + ']=' + $(this).val()
				});

				$.ajax({
					type: "POST",
					url: "/a/postserv.php?mode=commit&type=cell&id=<?=$id?>",
					data: "subject=" + $("#postFormSubject").val() + "&number=" + $("#postFormNumber").val() + "&title=" + $("#postFormTitle").val() + "&content=" + $("#postFormContent").val() + "&href=" + $("#postFormURL").val() + legendData,
					success: function(data){
						$("#post_cell_<?=$id?>").html(data);
						var bgSwap = $("#post_cell_<?=$id?>").children().css("background");
						$("#post_cell_<?=$id?>").parent().css({"background" : bgSwap});
						$("#post_cell_<?=$id?>").children().css({"background" : "none"});
						chGreybox.close();
					}
				});
			});
		</script>
<?php
		}//if (community college)
		else
		{
			echo 'This cell does not exist in the database';
		}

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
			$("#postFormTitle").focus();
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

		$legendData = serialize($_POST['legend']);

		// Update the database
		$DB->Update('post_cell', array(
			'legend'=>$legendData,
			'course_subject'=>$subject,
			'course_number'=>$number,
			'course_title'=>$title,
			'content'=>$_POST['content'],
			'href' => $href), intval($id));

		$background = '';
		foreach($_POST['legend'] as $id=>$toggle)
			if($toggle == 1)
				$background .= $id . '-';
		$background = ($background != '') ? 'url(/c/images/legend/' . substr($background, 0, -1) . '.png) top left no-repeat;' : 'none;';

		// Decide what we should draw back to the page
		if($subject != '' && $number != '')
			echo '<a href="javascript:void(0);" style="background: ' . $background . '">' . $subject . ' ' . $number . '<br />' . $title . '</a>';
		else
			echo ($link?'<a href="javascript:void(0);" style="background: ' . $background . '">':'<span style="background: ' . $background . '">') . $_POST['content'] . ($link?'</a>':'</span>');
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

	function commitSwap($fromID, $toID)
	{
		global $DB;
		$rows = $DB->MultiQuery("SELECT `id`, `row_num`, `col_id` FROM `post_cell` WHERE `id` = '" . intval($fromID) . "' OR `id` = '" . intval($toID) . "'");

		$DB->Update('post_cell', array('row_num'=>$rows[0]['row_num'], 'col_id'=>$rows[0]['col_id']), $rows[1]['id']);
		$DB->Update('post_cell', array('row_num'=>$rows[1]['row_num'], 'col_id'=>$rows[1]['col_id']), $rows[0]['id']);
	}//end function commitSwap

	/*****************************/
	/******* FORM PRINTERS *******/
	/*****************************/

	function getHSFormHTML(&$cell = NULL)
	{
		if(!$cell)
			$cell = array('content'=>'', 'href'=>'', 'legend'=>'');

		$legend = @unserialize($cell['legend']);
		getLegendHTML($legend);

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

		if(!$cell)
			$cell = array('content'=>'', 'href'=>'', 'legend'=>'', 'course_subject'=>'', 'course_number'=>'', 'course_title'=>'');

		$legend = @unserialize($cell['legend']);
		getLegendHTML($legend);
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

	/**
	 * This takes a legend array and prints the HTML/javascript for the Legend
	 * This assumes that a <form> tag exists.. although with AJAX it doesn't really matter
	 */
	function getLegendHTML($legend)
	{
		global $DB;
		
		if(!is_array($legend))
			$legend = array('1'=>'0', '2'=>'0', '3'=>'0', '4'=>'0', '5'=>'0', '6'=>'0', '7'=>'0', '8'=>'0');

		echo '<div style="margin-bottom: 5px;">Legend Symbols:</div>', "\n";
		$legendList = $DB->MultiQuery("SELECT * FROM `post_legend` WHERE `text` != '' ORDER BY `id` ASC");
		foreach($legendList as $item)
		{
			$checked = ($legend[$item['id']] == 1) ? 'c' : 'b';
?>
<img style="margin-right: 20px; cursor: pointer;" class="post_legend_icon" id="legend_icon_<?=$item['id']?>" src="/c/images/legend/<?=$checked . $item['id']?>.png" alt="<?=$item['text']?>" />
<input type="text" class="post_legend_input" id="legend_input_<?=$item['id']?>" style="display: none;" value="<?=$legend[$item['id']]?>" />
<?php
		}
?>
		<div style="clear: both; margin-top: 5px;" id="post_legend_help">Accrediation Articulation</div>
		<div style="clear: both; height: 15px;"></div>
		<script language="JavaScript" type="text/javascript">

	var legendText = {<?php
	$array = '';
	foreach($legendList as $item)
		$array .= '"' . $item['id'] . '" : "' . str_replace('"', '\"', $item['text']) . '", ';
	echo substr($array, 0 ,-2) . '};', "\n";
	?>

	$(".post_legend_icon").hover(function(){
		var id = $(this).attr("id").split("_");
		id = id[2];
		$("#post_legend_help").html(legendText[id]);
	}, function() {
		$("#post_legend_help").html("&nbsp;");
	});

	$(".post_legend_icon").click(function(){
		var id = $(this).attr("id").split("_");
		id = id[2];

		var newValue = ($("#legend_input_" + id).val() == "0") ? "1" : "0";
		$("#legend_input_" + id).val(newValue);
		if(newValue == "1")
			$("#legend_icon_" + id).attr("src", "/c/images/legend/c" + id + ".png");
		else
			$("#legend_icon_" + id).attr("src", "/c/images/legend/b" + id + ".png");
	});

		</script>
<?php
	}//end function getLegendHTML
?>