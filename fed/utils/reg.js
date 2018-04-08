

// 正整数数字 合法小数等
function isNumber(s)
{
	return /((^[1-9]+\d*$)|(^[1-9]+\d*\.\d+$)|(^0\.\d+$))/.test(s);
}

function is_email(str)
{
	return /^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i.test(str);
}

function is_tel(str)
{
	return /^1[34578][0-9]{9}$/.test(str);
}