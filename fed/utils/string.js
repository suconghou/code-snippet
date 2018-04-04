// 一行代码实现一个简单的模板字符串替换
String.prototype.render = function (context) {
  return this.replace(/{{(.*?)}}/g, (match, key) => context[key.trim()]);
};