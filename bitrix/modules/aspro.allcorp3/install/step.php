<?if(!check_bitrix_sessid()) return;?>
<style type="text/css">.adm-info-message-wrap + .adm-info-message-wrap .adm-info-message{margin-top: 0 !important;}</style>
<?=CAdminMessage::ShowNote(GetMessage('ALLCORP3_MOD_INST_OK'));?>
<?=BeginNote('align="left"');?>
<?=GetMessage('ALLCORP3_MOD_INST_NOTE')?>
<?=EndNote();?>
<form action="/bitrix/admin/wizard_list.php?lang=<?=LANGUAGE_ID;?>">
	<input type="submit" name="" value="<?=GetMessage('ALLCORP3_OPEN_WIZARDS_LIST')?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID;?>">
<form>