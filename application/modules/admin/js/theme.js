var Theme = {
	
	count: false,

	initialize: function()
	{
		Theme.count = $("#theme_overflow a").length;
	},

	scroll: function(to)
	{
		// Arrays start on 0 - CSS selctors don't.
		to = to + 1;

		if(Theme.count == false)
		{
			Theme.initialize();
		}

		var margin = -(248 * (to - 2)) - 25;

		$("#theme_overflow").css({marginLeft:margin});
		$("#theme_overflow img:not(:nth-child(" + to + "))").transition({scale:0.85,opacity:0.7,perspective:'233px',rotateY: '0deg'}, 500);
		$("#theme_overflow img:nth-child(" + to + ")").transition({scale:1,opacity:1,perspective:'233px',rotateY: '10deg'}, 500);

		$(".active_theme").removeClass("active_theme");
		$("#theme_" + (to - 1)).addClass("active_theme");
	},

	select: function(name)
	{
			Swal.fire({
			  title: '您确定要切换至主题“'+ name +'”吗？',
			  showDenyButton: true,
			  showCancelButton: false,
			  confirmButtonText: '切换',
			  denyButtonText: '取消',
			  icon: 'question'
			}).then((result) => {
				if (result.isConfirmed) {
					$.get(Config.URL + "admin/theme/set/" + name, function(data) {
						if(data == "yes") {
							Swal.fire({
							  title: '主题已保存！',
							  showDenyButton: false,
							  showCancelButton: false,
							  confirmButtonText: '确定',
							  icon: 'success'
							}).then((result) => {
								//Remove dsiabled from all button and set text to enabled
								$("#all_themes .theme_action button").removeAttr("disabled");
								$("#all_themes .theme_action button").text("启用");
								
								//Add to button disabled state and current Text
								$("#btn-"+ name).attr("disabled", "true");
								$("#btn-"+ name).text("当前");
								window.location.reload();
							});
						} else {
							Swal.fire(data, '', 'info');
						}
					});
				}
			})
	}
}