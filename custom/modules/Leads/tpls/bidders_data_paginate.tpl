<tr class='pagination'>
        <td colspan='19'>
            <table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
            <tr>
            	{assign var=GREEN_DOLLER value='<img align="absmiddle" src="custom/themes/default/images/green_money.gif" title="'|cat:$APP.LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT|cat:'"  alt="'|cat:$APP.LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT|cat:'" /> '}
                <td width="64%" style="font-size: 12px;">{$MOD.LBL_CONVERTED_BIDDERS_MSG|replace:' * ':$GREEN_DOLLER}</td>
                <td width='36%' align="right" class='paginationChangeButtons' style="padding: 0px;">
                    {if $pageData.urls.startPage}
                            <button type='button' id='listViewStartButton' name='listViewStartButton' title='{$navStrings.start}' class='list-view-pagination-button' {if $prerow}onclick='return sListView.save_checks(0, "{$moduleString}");'{else} onClick='getURLdata("{$pageData.urls.startPage}")' {/if}>
								<span class="suitepicon suitepicon-action-first"></span>
                            </button>
                    {else}
                            <button type='button' id='listViewStartButton' name='listViewStartButton' title='{$navStrings.start}' class='list-view-pagination-button' disabled='disabled'>
								<span class="suitepicon suitepicon-action-first"></span>
                            </button>
                    {/if}
                    {if $pageData.urls.prevPage}
                            <button type='button' id='listViewPrevButton' name='listViewPrevButton' title='{$navStrings.previous}' class='list-view-pagination-button' {if $prerow}onclick='return sListView.save_checks({$pageData.offsets.prev}, "{$moduleString}")' {else} onClick='getURLdata("{$pageData.urls.prevPage}")'{/if}>
                                <span class="suitepicon suitepicon-action-left"></span>						
                            </button>
                    {else}
                            <button type='button' id='listViewPrevButton' name='listViewPrevButton' class='list-view-pagination-button' title='{$navStrings.previous}' disabled='disabled'>
                                <span class="suitepicon suitepicon-action-left"></span>
                            </button>
                    {/if}
                            <span class='pageNumbers'>({if $pageData.offsets.lastOffsetOnPage == 0}0{else}{$pageData.offsets.current+1}{/if} - {$pageData.offsets.lastOffsetOnPage} {$navStrings.of} {if $pageData.offsets.totalCounted}{$pageData.offsets.total}{else}{$pageData.offsets.total}{if $pageData.offsets.lastOffsetOnPage != $pageData.offsets.total}+{/if}{/if})</span>
                    {if $pageData.urls.nextPage}
                            <button type='button' id='listViewNextButton' name='listViewNextButton' title='{$navStrings.next}' class='list-view-pagination-button' {if $prerow}onclick='return sListView.save_checks({$pageData.offsets.next}, "{$moduleString}")' {else} onClick='getURLdata("{$pageData.urls.nextPage}")'{/if}>
								<span class="suitepicon suitepicon-action-right"></span>
                            </button>
                    {else}
                            <button type='button' id='listViewNextButton' name='listViewNextButton' class='list-view-pagination-button' title='{$navStrings.next}' disabled='disabled'>
								<span class="suitepicon suitepicon-action-right"></span>
                            </button>
                    {/if}
                    {if $pageData.urls.endPage  && $pageData.offsets.total != $pageData.offsets.lastOffsetOnPage}
                            <button type='button' id='listViewEndButton' name='listViewEndButton' title='{$navStrings.end}' class='list-view-pagination-button' {if $prerow}onclick='return sListView.save_checks("end", "{$moduleString}")' {else} onClick='getURLdata("{$pageData.urls.endPage}")'{/if}>
                             	<span class="suitepicon suitepicon-action-last"></span>					
                            </button>
                    {elseif !$pageData.offsets.totalCounted || $pageData.offsets.total == $pageData.offsets.lastOffsetOnPage}
                            <button type='button' id='listViewEndButton' name='listViewEndButton' title='{$navStrings.end}' class='list-view-pagination-button' disabled='disabled'>
								<span class="suitepicon suitepicon-action-last"></span>	
                            </button>
                    {/if}
            </td>
          </tr>
      </table>
    </td>
</tr>