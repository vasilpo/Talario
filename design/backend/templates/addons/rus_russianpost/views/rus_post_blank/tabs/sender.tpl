<fieldset>
    {include file="common/subheader.tpl" title=__("rus_post_blank.subheader.cod_to")}
    <div id="post_blank_to" class="collapse in">
        <div class="control-group">
            <label for="blank_whom" class="control-label">{__("rus_post_blank.whom")} <small>({__("rus_post_blank.line_1")})</small></label>
            <div class="controls">
                <input type="text" name="blank_data[whom]" id="blank_whom" value="{$pre_data.company}" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label for="blank_whom2" class="control-label">{__("rus_post_blank.whom")} <small>({__("rus_post_blank.line_2")})</small></label>
            <div class="controls">
                <input type="text" name="blank_data[whom2]" id="blank_whom2" value="{$pre_data.company2}" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label for="blank_where" class="control-label">{__("rus_post_blank.where")} <small>({__("rus_post_blank.line_1")})</small></label>
            <div class="controls">
                <input type="text" name="blank_data[where]" id="blank_where" value="{$pre_data.address}" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label for="blank_where2" class="control-label">{__("rus_post_blank.where")} <small>({__("rus_post_blank.line_2")})</small></label>
            <div class="controls">
                <input type="text" name="blank_data[where2]" id="blank_where2" value="{$pre_data.address2}" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label for="blank_index" class="control-label">{__("rus_post_blank.index")}</small></label>
            <div class="controls">
                <input type="text" name="blank_data[index]" id="blank_index" value="{$pre_data.index}" size="40" class="input-large" maxlength="6"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="blank_company_phone">{__("rus_post_blank.company_phone")}</label>
            <div class="controls">
                <input type="text" name="blank_data[company_phone]" id="blank_company_phone" value="{$pre_data.company_phone}" size="40" class="input-large" maxlength="10"/>
            </div>
        </div>

        {include file="common/subheader.tpl" title=__("rus_post_blank.company_info")}
        <div class="control-group">
            <label for="blank_inn" class="control-label">{__("rus_post_blank.inn")}</label>
            <div class="controls">
                <input type="text" name="blank_data[inn]" id="blank_inn" value="{$pre_data.inn}" size="40" class="input-large" maxlength="12"/>
            </div>
        </div>
        <div class="control-group">
            <label for="blank_kor" class="control-label">{__("rus_post_blank.kor")}</label>
            <div class="controls">
                <input type="text" name="blank_data[kor]" id="blank_kor" value="{$pre_data.kor}" size="40" class="input-large" maxlength="20"/>
            </div>
        </div>
        <div class="control-group">
            <label for="blank_bank" class="control-label">{__("rus_post_blank.bank")}</label>
            <div class="controls">
                <input type="text" name="blank_data[bank]" id="blank_bank" value="{$pre_data.bank}" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label for="blank_ras" class="control-label">{__("rus_post_blank.ras")}</label>
            <div class="controls">
                <input type="text" name="blank_data[ras]" id="blank_ras" value="{$pre_data.ras}" size="40" class="input-large" maxlength="20"/>
            </div>
        </div>
        <div class="control-group">
            <label for="blank_bik" class="control-label">{__("rus_post_blank.bik")}</label>
            <div class="controls">
                <input type="text" name="blank_data[bik]" id="blank_bik" value="{$pre_data.bik}" size="40" class="input-large" maxlength="9"/>
            </div>
        </div>
    </div>
</fieldset>