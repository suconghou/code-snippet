



function buildQuery(params){
    var esc = encodeURIComponent;
    return Object.keys(params).map(k => esc(k) + '=' + esc(params[k])).join('&');
}
function buildQuery(params){
    var esc = encodeURIComponent;
    return Object.keys(params).map(function(k) {return esc(k) + '=' + esc(params[k]);}).join('&');
}

function query(o){
	return Object.entries(o).reduce((arr, [k, v]) => arr.concat(encodeURIComponent(k) + '=' + encodeURIComponent(v)), []).join('&')
}


function getParam()
{
	var data = decodeURIComponent(location.href).split("?")[1].split("&");
	var param = {};
	for(var i = 0; i<data .length; i++)
	{
		param[data [i].split("=")[0]] = data [i].split("=")[1];
	}
	return param;
}
// 类似于PHP中的`$_GET`
var $_GET = (function()
{
	var url = decodeURIComponent(location.href);
	var u = url.split( "?" );
	if ( typeof (u[1]) == "string" )
	{
		u = u[1].split( "&" );
		var get = {};
		for ( var i in u)
		{
			var j = u[i].split( "=" );
			get[j[0]] = j[1];
		}
		return get;
	}
	else
	{
		return {};
	}
})();

//给出要获取的参数,每次都会解析一遍
function getPar(par)
{
	//获取当前URL
	var local_url = decodeURIComponent(location.href);
	//获取要取得的get参数位置
	var get = local_url.indexOf(par + "=" );
	if (get == -1)
	{
		return false ;
	}
	//截取字符串
	var get_par = local_url.slice(par.length + get + 1);
	//判断截取后的字符串是否还有其他get参数
	var nextPar = get_par.indexOf( "&" );
	if (nextPar != -1)
	{
		get_par = get_par.slice(0, nextPar);
	}
	return get_par;
}
//QueryString
function QueryString(item)
{
	var value = location.search.match(new RegExp('[\?\&]' + item + '=([^\&]*)(\&?)','i'));
	return value ? value[1] : value;
}
