<?php defined('SYSPATH') or die('No direct script access.');
/**
 * categorycsv view - Creates the download reports page that you see
 *
 
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Category CSV Ushahidi Plugin - https://github.com/jetherton/categorycsv
  
 */
?>
<div class="bg">
	<h2>
		<?php echo Kohana::lang('categorycsv.title'); ?><?php admin::reports_subtabs("csvcategory_download"); ?>
	</h2>
	<!-- report-form -->
	<div class="report-form">
		<?php
		if ($form_error) {
		?>
			<!-- red-box -->
			<div class="red-box">
				<h3><?php echo Kohana::lang('ui_main.error');?></h3>
				<ul>
				<?php
				foreach ($errors as $error_item => $error_description)
				{
					// print "<li>" . $error_description . "</li>";
					print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
				}
				?>
				</ul>
			</div>
		<?php
		}
		?>
		<!-- column -->
		<div class="download_container">
			<p><?php echo Kohana::lang('ui_main.reports_download_csv');?>.</p>
			<span style="font-weight: bold; color: #00699b; display: block; padding-bottom: 5px;"><?php echo Kohana::lang('ui_main.choose_data_points');?>:</span>
			<?php print form::open(NULL, array('id' => 'reportForm', 'name' => 'reportForm')); ?>
			<table class="data_points">
				<tr>
					<td colspan="2">
						<!--<input type="checkbox" id="data_all" name="data_all" checked="checked" onclick="CheckAll(this.id)" /><strong><?php //echo strtoupper(Kohana::lang('ui_main.select_all'));?></strong>-->
						<div id="form_error1"></div>
					</td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_point[]', '1', TRUE); ?><?php echo Kohana::lang('ui_main.approved_reports');?></td>
					<td><?php print form::checkbox('data_include[]', '1', TRUE); ?><?php echo Kohana::lang('ui_main.include_location_information');?></td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_point[]', '2', TRUE); ?><?php echo Kohana::lang('ui_main.verified_reports');?></td>
					<td><?php print form::checkbox('data_include[]', '2', TRUE); ?><?php echo Kohana::lang('ui_main.include_description');?></td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_include[]', '3', TRUE); ?><?php echo Kohana::lang('ui_main.include_categories');?></td>
				</tr>
                                <tr>
                                        <td><?php print form::checkbox('data_point[]', '4', TRUE); ?><?php echo Kohana::lang('ui_main.reports');?> <?php echo Kohana::lang('ui_main.awaiting_verification');?></td>
					<td><?php print form::checkbox('data_point[]', '3', TRUE); ?><?php echo Kohana::lang('ui_main.reports');?> <?php echo Kohana::lang('ui_main.awaiting_approval');?></td>
                                </tr>
                                <tr>
                                        <td><?php print form::checkbox('data_include[]','5',TRUE); ?><?php echo Kohana::lang('ui_main.include_longitude');?></td>
					<td><?php print form::checkbox('data_include[]','4',TRUE); ?><?php echo Kohana::lang('ui_main.include_latitude');?></td>

                                </tr>
				<tr>					
					<td><?php print form::checkbox('strip_html','true', TRUE); ?><?php echo Kohana::lang('categorycsv.tags');?></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="date-box">
							<h4><?php echo Kohana::lang('ui_admin.from_date');?>: <span><?php echo Kohana::lang('ui_main.date_format');?></span></h4>
							<?php print form::input('from_date', $form['from_date'], ' class="text"'); ?>											    
						</div>
						<div class="date-box">
							<h4><?php echo Kohana::lang('ui_admin.to_date');?>: <span><?php echo Kohana::lang('ui_main.date_format');?></span></h4>
							<?php print form::input('to_date', $form['to_date'], ' class="text"'); ?>											    
						</div>
						<div id="form_error2"></div>
					</td>
				</tr>
			</table>
			<input id="save_only" type="image" src="<?php print url::base() ?>media/img/admin/btn-download.gif" class="save-rep-btn" />
			<?php print form::close(); ?>
		</div>
	</div>
</div>
