<style>
.nostyles, .mainTable {
  width:100%;
  margin:0 !important;
  border:none;
  table-layout:fixed;
}

.mainTable th {
  text-align:left;
}

.nostyles td {
  padding:0;
  border-left:none !important;
  border-bottom:none !important;
}

.nostyles td:first-child { border-right:1px dotted #D0D7DF; }
table.nostyles td:last-child { border-right:none !important; }

</style>
<?php if(count($vars['weblogs']) == 0) : ?>
<p style="margin-bottom:1.5em">You haven't created any weblogs yet. Go to the <a href="<?=BASE.AMP.'M=blog_admin'.AMP.'P=new_weblog';?>">Weblog Management</a> and create one first.</p>
<?php else : ?>
  
  <?=  $DSP->form_open('C=admin'.AMP.'M=utilities'.AMP.'P=save_extension_settings'.AMP.'name=single_sticky', array(), array('name' => 'single_sticky')) ?>

<table class="tableBorder" border="0" style="margin-top:18px; width:100%" cellspacing="0" cellpadding="0">
  <thead>
    <tr>
      <td class="tableHeading">Weblog</td>
      <td class="tableHeading"><?=lang('single_sticky_enable')?></td>
    </tr>
  </thead>
  <tbody>
  <?php
    $j = $i = 0;
    foreach($vars['weblogs'] as $weblog) :
  ?>
    <tr class="<?=($i%2) ? 'even' : 'odd';?>">
      <td class="tableCellOne" style="width:25%;"><b><?=$weblog['title']?></b></td>
      <td class="tableCellOne">
        <label><input type="radio" name="ss_enabled[<?=$weblog['id']?>]" value="y"<?php if($weblog['enabled'] === 'y') : ?> checked="checked"<?php endif; ?> /> <?=lang('yes')?></label>
        <label><input type="radio" name="ss_enabled[<?=$weblog['id']?>]" value="n"<?php if($weblog['enabled'] === 'n') : ?> checked="checked"<?php endif; ?> /> <?=lang('no')?></label>
      </td>
<!--      
      <td class="tableCellOne">
        <label><input type="checkbox" name="restrict[<?=$weblog['id']?>]" value="no" /> <?=lang('no')?></label>
      </td>
-->
    </tr>
  <?php
    $i++;
    endforeach;
  ?>
  </tbody>
</table>
<div style="padding:10px 0"><input type="submit" value="Save settings" class="submit" /></div>
<?=  $DSP->form_close(); ?>
<?php endif; ?>