function setCookie(cookiename, cookievalue, hours)
{
	var date = new Date();
	date.setTime(date.getTime() + Number(hours) * 3600 * 1000);
	document.cookie = cookiename + "=" + cookievalue + "; path=/;expires = " + date.toGMTString();
}

function getCookie (name)
{
	 if(document.cookie.indexOf(name)!=-1) return document.cookie.split(name+'=')[1].split(';')[0]
}