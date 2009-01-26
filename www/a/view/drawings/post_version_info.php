<?php

$main_table = 'post_drawing_main';
$drawings_table = 'post_drawings';

$published_link = 'http://'.$_SERVER['SERVER_NAME'].'/c/post/%%/##.html';
$xml_link = 'http://'.$_SERVER['SERVER_NAME'].'/c/post/%%/##.xml';
$accessible_link = 'http://'.$_SERVER['SERVER_NAME'].'/c/post/text/%%/##.html';


$drawing = GetDrawingInfo($version_id, $MODE);
$drawing_main = $DB->LoadRecord($main_table, $drawing['parent_id']);

switch( $MODE ) {
case 'pathways':
	$embed_code = '<iframe width="800" height="600" src="http://'.$_SERVER['SERVER_NAME'].'/c/published/'.$drawing_main['code'].'.html" frameborder="0" scrolling="no"></iframe>';
	break;
case 'ccti':
	$embed_code = '<iframe width="800" height="600" src="http://'.$_SERVER['SERVER_NAME'].'/c/post/'.$drawing_main['code'].'.html" frameborder="0" scrolling="no"></iframe>';
	break;
}

$created = ($drawing['created_by']==''?array('name'=>''):$DB->SingleQuery("SELECT CONCAT(first_name,' ',last_name) AS name FROM users WHERE id=".$drawing['created_by']));
$modified = ($drawing['last_modified_by']==''?array('name'=>''):$DB->SingleQuery("SELECT CONCAT(first_name,' ',last_name) AS name FROM users WHERE id=".$drawing['last_modified_by']));

$school_name = $DB->GetValue('school_name', 'schools', $drawing_main['school_id']);

$siblings = $DB->SingleQuery("SELECT COUNT(*) AS num FROM drawings WHERE parent_id=".$drawing_main['id']);

?>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" id="version_form" name="version_form">

<a href="<?= $_SERVER['PHP_SELF'] ?>" class="edit">back</a>

<p>
<table>
<tr>
	<th width="80">Drawing</th>
	<td><span class="drawing_title"><?= $drawing_main['name'] ?></span>
		<a href="<?= $_SERVER['PHP_SELF'].'?action=drawing_info&id='.$drawing_main['id'] ?>"><img src="/common/silk/cog.png" width="16" height="16" /></a></td>
</tr>
<tr>
	<th>Version</th>
	<td><div class="version_title"><?= $drawing['version_num'].($drawing['published']==1?" (Published)":"") ?></div></td>
</tr>
<tr>
	<th>Organization</th>
	<td><b><?= $school_name ?></b></td>
</tr>
<tr>
	<th>Created</th>
	<td><?php
		echo ($drawing['date_created']==''?'':$DB->Date("m/d/Y g:ia",$drawing['date_created'])).' <a href="/a/users.php?id='.$drawing['created_by'].'">'.$created['name'].'</a>';
	?></td>
</tr>
<tr>
	<th>Modified</th>
	<td><?php
		echo ($drawing['last_modified']==''?'':$DB->Date("m/d/Y g:ia",$drawing['last_modified'])).' <a href="/a/users.php?id='.$drawing['last_modified_by'].'">'.$modified['name'].'</a>';
	?></td>
</tr>
<tr>
	<th>Actions</th>
	<td>
		<a href="/a/post_drawings.php?action=draw&version_id=<?= $drawing['id'] ?>">Draw</a> &nbsp;
		<a href="javascript:preview_drawing(<?= "'".$drawing_main['code']."', ".$drawing['version_num'] ?>)">Preview</a>
	</td>
</tr>
<tr>
	<th>Note</th>
	<td>
		<div id="note_fixed"><span id="note_value"><?= $drawing['note'] ?></span> <a href="javascript:showNoteChange()" class="tiny">edit</a></div>
		<div id="note_edit" style="display:none">
			<input type="text" id="version_note" name="name" size="60" value="<?= $drawing['note'] ?>">
			<input type="button" class="submit tiny" value="Save" id="submitButton" onclick="savenote()">
		</div>
	</td>
</tr>
<tr>
	<th valign="top">Link</th>
	<td><?php $url = str_replace(array('%%','##'), array($drawing_main['code'], $drawing['version_num']), $published_link); ?>
	<input type="text" style="width:560px" value="<?= $url ?>" onclick="this.select()" />
	</td>
</tr>
<!--
<tr>
	<th valign="top">XML</th>
	<td><?php $url = str_replace(array('%%','##'), array($drawing_main['code'], $drawing['version_num']), $xml_link); ?>
		<a href="<?= $url ?>"><?= $url ?></a>
	</td>
</tr>
<tr>
	<th valign="top">Accessible</th>
	<td><?php $url = str_replace(array('%%','##'), array($drawing_main['code'], $drawing['version_num']), $accessible_link); ?>
		<a href="<?= $url ?>"><?= $url ?></a>
	</td>
</tr>
-->
<tr>
	<td>&nbsp;</td>
	<td><!--These are permanent links -->This is a permanent link to <b>this version</b> of the drawing. You can give this link to people to share your in-progress drawing easily.</td>
</tr>
<?php
	/* who can delete versions?
		1. admins
		2. school admins & webmasters at the same school
		3. the owner of the version
		4. the owner of the drawing
	   no version may be deleted if it's "published"
	   a version may not be deleted if it's the only one for that drawing
	*/
	if( $version_id != "" &&
		$drawing['published'] == 0 &&
		$siblings['num'] > 1 &&
		(
			IsAdmin()
			|| (IsSchoolAdmin() && $_SESSION['school_id'] == $drawing_main['school_id'] )
			|| $drawing['created_by'] == $_SESSION['user_id']
			|| $drawing_main['created_by'] == $_SESSION['user_id']
	    )
	) { ?>
<tr>
	<th>Delete</th>
	<td width="545">
		<b>There is no way to recover deleted drawings!</b><br>
		If you are sure you want to delete this version, click the link below:<br>
		<p><a href="javascript:deleteConfirm()">Delete this version</a></p>
		<div id="deleteConfirm"></div>
	</td>
</tr>
<?php
		$can_delete = true;
	} else {
		$can_delete = false;
	}
?>
</table>

<?php
if( $version_id != "" && $drawing['published'] == 0 && (IsAdmin() || $_SESSION['school_id'] == $drawing_main['school_id']) ) {
	?>
	<p><input type="button" name="publish" class="publish_link" onclick="publishVersion()" value="Publish this version"></p>
	<?php
}
if( $drawing['published'] ) {
	echo '<div class="publish_link_inactive" style="width:100px;text-align:center">Published</div>';
}
?>

<input type="hidden" name="action" id="action_field" value="">
<input type="hidden" name="drawing_id" value="<?= $drawing['id'] ?>">
</form>

<br /><br />

<h3>Drawing Structure</h3>
<br />
<table class="post_drawing_structure">
<tr>
	<th width="80">Block Diagram</th>
	<td>
		<?php
			$post = POSTChart::create($drawing['id']);
			$post->displayMini();
		?>
	</td>
</tr>
<tr>
	<th>Help</th>
	<td>The diagram above represents your drawing. Shaded cells have content entered in them, white cells are empty. Changing the number of rows or columns of your drawing is a <b>destructive</b> operation. For example, if you remove 3 columns, the contents of those columns will be permanently erased.</td>
</tr>
<?php
if( $post->type == 'CC' )
{
	?>
	<tr>
		<th>Terms</th>
		<td id="num_terms">
			<div class="current"><span class="post_large_number"><?= $post->numRows ?></span> <a href="javascript:changeTerms()">change</a></div>
			<div class="editing" style="display:none">
				<?php
					$range = array(0,3,6,9,12);
					$options = array();
					foreach( $range as $i )
					{
						$options[$i] = $i;
					}
					echo GenerateSelectBox($options, 'new_num_terms', $post->numRows);	
				?>
				<a href="javascript:saveConfig('terms', 'num_terms')">save</a>
			</div>
		</td>
	</tr>
<?php
}
?>
<tr>
	<th>Blank Rows</th>
	<td id="num_extra_rows">
		<div class="current"><span class="post_large_number"><?= $post->numExtraRows ?></span> <a href="javascript:changeExtraRows()">change</a></div>
		<div class="editing" style="display:none">
			<?php
				$range = range(0, 9);
				$options = array();
				foreach( $range as $i )
				{
					$options[$i] = $i;
				}
				echo GenerateSelectBox($options, 'new_num_extra_rows', $post->numExtraRows);			
			?>
			<a href="javascript:saveConfig('extra_rows', 'num_extra_rows')">save</a>
		</div>
	</td>
</tr>
<tr>
	<th>Columns</th>
	<td id="num_columns">
		<div class="current"><span class="post_large_number"><?= $post->numCols ?></span> <a href="javascript:changeColumns()">change</a></div>
		<div class="editing" style="display:none">
			<?php
				$range = range(3, 9);
				$options = array();
				foreach( $range as $i )
				{
					$options[$i] = $i;
				}
				echo GenerateSelectBox($options, 'new_num_columns', $post->numCols);			
			?>
			<a href="javascript:saveConfig('columns', 'num_columns')">save</a>
		</div>
	</td>
</tr>
</table>


<script type="text/javascript">

var $j = jQuery.noConflict();

function changeTerms() {
	$j('#num_terms .current').css({display: 'none'});
	$j('#num_terms .editing').css({display: 'block'});
}

function changeExtraRows() {
	$j('#num_extra_rows .current').css({display: 'none'});
	$j('#num_extra_rows .editing').css({display: 'block'});
}

function changeColumns() {
	$j('#num_columns .current').css({display: 'none'});
	$j('#num_columns .editing').css({display: 'block'});
}

function saveConfig(change, did) {
	$j.post('post_drawings.php', {
			action: 'config',
			drawing_id: <?= $version_id ?>,
			change: change,
			value: $j('#'+did+' select').attr('value')
		},
		function() {
			window.location.reload();
		}
	);
}



function publishVersion() {
	getLayer('action_field').value = "publish";
	getLayer('version_form').submit();
}

function savenote() {
	var note = getLayer('version_note');
	ajaxCallback(cbNoteChanged, '/a/drawings_post.php?mode=<?= $MODE ?>&drawing_id=<?= $version_id ?>&note='+note.value);
}

function cbNoteChanged() {
	getLayer('note_value').innerHTML = getLayer('version_note').value;
	getLayer('note_edit').style.display = 'none';
	getLayer('note_fixed').style.display = 'block';
}

function showNoteChange() {
	getLayer('note_edit').style.display = 'block';
	getLayer('note_fixed').style.display = 'none';
}

function preview_drawing(code,version) {
	chGreybox.create('<div id="dpcontainer"><iframe src="/c/<?= $MODE=='pathways'?'version':'post' ?>/'+code+'/'+version+'.html"></iframe></div>',800,600);
}

<?php if( $can_delete ) { ?>
function deleteConfirm() {
	getLayer('deleteConfirm').innerHTML = 'Are you sure? <a href="javascript:doDelete()">Yes</a>';
}
function doDelete() {
	getLayer('action_field').value = 'delete';
	getLayer('version_form').submit();
}
<?php } ?>

</script>