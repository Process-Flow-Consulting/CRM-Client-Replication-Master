
{foreach from=$AR_DUPE_DETAILS key=ST_PAR_LEAD_ID item=AR_LEAD_ID}
<div>
    <ul><li>
            Created Primary Project Lead - <a href="index.php?module=Leads&action=DetailView&record={$ST_PAR_LEAD_ID}"> {$AR_DUPE_MAP[$ST_PAR_LEAD_ID]}</a><br/>
            <br/><strong>Below are the duplicate project leads </strong>:
            <br/>
        </li>
        <ul>
        {foreach from=$AR_LEAD_ID item=ST_CHLD_LEAD_ID }
        <li>{$AR_DUPE_MAP[$ST_CHLD_LEAD_ID]}</li>
        {/foreach}
        </ul>
    </ul>
   
</div>	

{/foreach}
 <input type="button" onclick="window.location.href='index.php?module=Leads&action=index'" value="Back" />
