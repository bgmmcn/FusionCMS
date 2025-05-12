<div class="card mb-3">
	<div class="card-body">
	<table class="table table-responsive-md table-hover">
		<thead>
			<tr>
				<th>缓存类型</th>
				<th>大小</th>
			</tr>
		</thead>
		<tbody>
		<tr>
			<td>物品缓存</td>
			<td id="row_item">{$item.files} 个文件 ({$item.sizeString})</td>
		</tr>
		<tr>
			<td>网站缓存</td>
			<td id="row_website">{$website.files} 个文件 ({$website.sizeString})</td>
		</tr>
		<tr>
			<td>主题压缩缓存</td>
			<td id="row_theme">{$theme.files} 个文件 ({$theme.sizeString})</td>
		</tr>
		<tr>
			<td><b>总计</b></td>
			<td id="row_total"><b>{$total.files} 个文件 ({$total.size})</b></td>
		</tr>
		</tbody>
	</table>
	</div>
</div>