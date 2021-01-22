<div>
    <ul><li>
            Created Primary Project Lead - <a href="index.php?module=Leads&action=DetailView&record={$OB_LEAD_DATA->id}"> {$OB_LEAD_DATA->project_title}</a><br/>
            <br/><strong>Below are the duplicate project leads </strong>:
            <br/>
        </li>
        <ul>
        {foreach from=$AR_LEAD_DATA item=data }
        {if $data->id neq $data->parent_lead_id}
        <li>{$data->project_title}</li>
        {/if}
        {/foreach}
        </ul>
    </ul>
    <input type="button" onclick="window.location.href='index.php?module=Leads&action=index'" value="Back" />
</div>
