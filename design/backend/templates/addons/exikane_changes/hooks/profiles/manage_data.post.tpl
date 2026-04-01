<td width="14%" class="row-status" data-th="{__("registration_date")}">
    {if $user.timestamp}
        {$user.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
    {/if}
</td>
