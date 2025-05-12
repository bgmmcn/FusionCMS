function getCookie(c_name) {
	var i, x, y, ARRcookies = document.cookie.split(";");

	for(i = 0; i < ARRcookies.length;i++) {
		x = ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y = ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		x = x.replace(/^\s+|\s+$/g,"");

		if(x == c_name)
			return unescape(y);
	}
}

function setCookie(c_name,value,exdays) {
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value = escape(value) + ((exdays == null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie = c_name + "=" + c_value;
}

$(document).ready(function() {
	if (getCookie("acceptCookies") == null){
		Swal.fire({
			title: '您接受Cookie吗？',
			text: '本网站使用Cookie以确保您获得最佳体验。',
			showDenyButton: true,
			showCancelButton: false,
			confirmButtonText: '接受',
			denyButtonText: '取消',
			backdrop: true,
			allowOutsideClick: () => {
				const popup = Swal.getPopup();
				
				popup.classList.remove('swal2-show');
				
				setTimeout(() => {
					popup.classList.add('animate__animated', 'animate__headShake')
				});
				
				setTimeout(() => {
					popup.classList.remove('animate__animated', 'animate__headShake')
				}, 500);
				
				return false;
			}
		}).then((result) => {
			if (result.isConfirmed) {
				setCookie("acceptCookies","2","30");
			}
		});
	}
});