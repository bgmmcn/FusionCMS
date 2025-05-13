<div class="card">
	<div class="card-header">
		过去一周的失败订单
	</div>
	<div class="card-body">
	<span>
		此处显示的订单因系统错误失败。若错误未立即发生，部分物品可能已发放。请人工核查是否需要给用户退款。
	</span>

		{if $failed}
		<table class="table table-responsive-md table-hover">
			{foreach from=$failed item=failed_log}
					<tr>
						<td width="20%">{date("Y/m/d", $failed_log.timestamp)}</td>
						<td width="16%">
							<a href="{$url}admin/accounts/get/{$failed_log.user_id}" target="_blank">
								{$failed_log.username}
							</a>
						</td>
						
						<td width="35%">
							{if $failed_log.vp_cost}<img src="{$url}application/images/icons/lightning.png" align="absmiddle" style="margin:0px;opacity:1;" /> <b>{$failed_log.vp_cost} 虚拟点</b>&nbsp;&nbsp;&nbsp;{/if}
							{if $failed_log.dp_cost}<img src="{$url}application/images/icons/coins.png" align="absmiddle"  style="margin:0px;opacity:1;"/> <b>{$failed_log.dp_cost} 捐赠点</b>{/if}
						</td>

						<td>
							<a data-bs-toggle="tooltip" data-placement="top" data-html="true" title="{foreach from=$failed_log.json item=item}{$item.itemName} 发送至 {$item.characterName}<br>{/foreach}">{count($failed_log.json)} 件物品</a>
						</td>
						
						{if hasPermission("canRefundOrders")}
							<td style="text-align:right;">
								<a class="relative font-sans font-normal text-sm inline-flex items-center justify-center leading-5 no-underline h-8 px-3 py-2 space-x-1 border nui-focus transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed hover:enabled:shadow-none text-muted-700 border-muted-300 dark:text-white dark:bg-muted-700 dark:border-muted-600 dark:hover:enabled:bg-muted-600 hover:enabled:bg-muted-50 dark:active:enabled:bg-muted-700/70 active:enabled:bg-muted-100 rounded-md" href="javascript:void(0)" onClick="Orders.refund({$failed_log.id}, this)">退款</a>
							</td>
						{/if}
					</tr>
			{/foreach}
			</table>
		{/if}
	</div>
</div>

<div class="card">
	<div class="card-header">
		最近 10 个成功订单
	</div>
	<div class="card-body">
	<form class="input-group mb-3" onSubmit="Orders.search('successful'); return false">
		<input class="form-control nui-focus border-muted-300 text-muted-600 placeholder:text-muted-300 dark:border-muted-700 dark:bg-muted-900/75 dark:text-muted-200 dark:placeholder:text-muted-500 dark:focus:border-muted-700 peer w-full border bg-white font-monospace transition-all duration-300 disabled:cursor-not-allowed disabled:opacity-75 px-2 h-10 py-2 text-sm leading-5 px-3 rounded" type="text" name="search_successful" id="search_successful" placeholder="搜索用户名" style="width:90%;margin-right:5px;"/>

		<button type="submit" class="btn btn-primary">搜索</button>
	</form>

	<span id="order_list_successful">
		{if $completed}
			{foreach from=$completed item=completed_log}
				<table class="table table-responsive-md table-hover">
					<tbody style="border-top:none">
					<tr>
						<td width="20%">{date("Y/m/d", $completed_log.timestamp)}</td>
						<td width="16%">
							<a href="{$url}admin/accounts/get/{$completed_log.user_id}" target="_blank">
								{$completed_log.username}
							</a>
						</td>
						
						<td width="35%">
							{if $completed_log.vp_cost}<img src="{$url}application/images/icons/lightning.png" align="absmiddle" style="margin:0px;opacity:1;" /> <b>{$completed_log.vp_cost} 虚拟点</b>&nbsp;&nbsp;&nbsp;{/if}
							{if $completed_log.dp_cost}<img src="{$url}application/images/icons/coins.png" align="absmiddle"  style="margin:0px;opacity:1;"/> <b>{$completed_log.dp_cost} 捐赠点</b>{/if}
						</td>

						<td>
							<a data-toggle="tooltip" data-placement="top" data-html="true" title="{foreach from=$completed_log.json item=item}{$item.itemName} 发送至 {$item.characterName}<br />{/foreach}">{count($completed_log.json)} 件物品</a>
						</td>
					</tbody>
					</tr>
				</table>
			{/foreach}
		{/if}
	</span>
	</div>
</div>