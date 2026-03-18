{hook name="llms:manage"}
    {capture name="mainbox"}

        {$r_url=$config.current_url|escape:url}

        <div class="items-container" id="manage_llms">

            <form id="llms_form" action="{""|fn_url}" method="post" name="llms_update_form" class="form-horizontal form-edit cm-ajax cm-disable-empty-files">
                <input type="hidden" name="result_ids" value="manage_llms" />

                <div id="manage_llms_content">

                    {if fn_allowed_for("ULTIMATE")}
                        <div class="control-group disable-overlay-wrap" id="field_llms_content">
                            {if !$runtime.company_id && !$runtime.simple_ultimate}
                                <div class="disable-overlay" id="llms_logo_disable_overlay"></div>
                            {/if}
                            <label for="elm_llms_edit" class="control-label">{__("edit_llms")}:</label>

                            <div class="controls" id="llms_content">
                                <textarea id="elm_llms_edit" name="llms_data[content]" cols="55" rows="24" class="span12">{$llms}</textarea>

                                {if !$runtime.company_id}
                                    <div class="right">
                                        {include file="buttons/update_for_all.tpl"
                                        display=true
                                        object_id="llms"
                                        name="llms_data[update_content]"
                                        hide_element="llms_uploader"
                                        component="llms.llms_uploader"
                                        }
                                    </div>
                                {/if}
                                <div class="muted description">{__("edit_llms_tooltip")}</div>
                            </div>
                        </div>
                    {else}
                        <div class="control-group">
                            <label for="elm_llms_edit" class="control-label">{__("edit_llms")}:</label>

                            <div class="controls">
                                <textarea id="elm_llms_edit" name="llms_data[content]" cols="55" rows="24" class="span12">{$llms}</textarea>
                                <div class="muted description">{__("edit_llms_tooltip")}</div>
                            </div>
                        </div>
                    {/if}

                    <!--manage_llms_content--></div>
            </form>

            <script>
                (function(_, $){
                    $(_.doc).on('click', '[data-ca-update-for-all="llms.llms_uploader"]', function(e){
                        $('#llms_uploader').toggleClass('disable-overlay-wrap');
                        $('#llms_logo_disable_overlay').toggleClass('disable-overlay');
                    });
                })(Tygh, Tygh.$);
            </script>

            <!--manage_llms--></div>

        {capture name="buttons"}
            {include file="buttons/save.tpl" but_name="dispatch[llms.update]" but_role="submit-link" but_target_form="llms_update_form"}
        {/capture}

    {/capture}

    {include file="common/mainbox.tpl"
        title=__("llms_title")
        content=$smarty.capture.mainbox
        buttons=$smarty.capture.buttons
        select_storefront=true
        show_all_storefront=!("MULTIVENDOR"|fn_allowed_for)
    }
{/hook}
