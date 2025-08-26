{* usergroups *}
<div class="control-group">
<label for="elm_banner_usergroups" class="control-label">{__("abt__ut2.banner.params.usergroups")}</label>
<div class="controls">
{include file="common/select_usergroups.tpl" id="ug_id" name="banner_data[abt__ut2_usergroup_ids]" usergroups=["type"=>"C", "status"=>["A", "H"]]|fn_get_usergroups:$smarty.const.DESCR_SL usergroup_ids=$banner.abt__ut2_usergroup_ids input_extra="" list_mode=false}
</div>
</div>