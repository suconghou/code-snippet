export const waitUntil = (c, fun, fall, dur = 50, maxTimes = 20) => {
  let times = 0;
  const funwarp = () => {
    const ret = c();
    times++;
    if (ret) {
      fun(times);
    } else if (times < maxTimes) {
      setTimeout(() => {
        funwarp();
      }, dur)
    } else {
      fall && fall(times);
    }
  }
  funwarp();
}


export const toWan = (v) => {
  const val = (v / 10000).toFixed(1);
  if (isNaN(val)) {
    return '';
  }
  const vint = parseInt(val);
  if (vint == val) {
    return vint;
  }
  return val;
}


// 返回被移除的元素 ，未找到返回[]
export const arrRemove = (arr, id) => {
  let index;
  for (let i = 0, len = arr.length; i < len; i++) {
    const item = arr[i];
    if (item.id === id) {
      return arr.splice(i, 1);
    }
  }
  return [];
}

export const arrInclude = (big, small) => {
  for (let i in small) {
    let item = small[i];
    if (big.indexOf(item) < 0) {
      return false;
    }
  }
  return true;
}

export const replace = (str, ...args) => {
  let s = str;
  args.forEach(arr => {
    s = s.replace(arr[0], arr[1]);
  })
  return s;
}

// 不带小数，最大支持亿兆
export const digitUppercase = n => {
  n = Math.abs(n);
  var s = '';
  var digit = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
  var unit = [
    ['', '万', '亿', '兆'],
    ['', '拾', '佰', '仟']
  ];
  for (let i = 0; i < unit[0].length; i++) {
    let p = '';
    for (let j = 0; j < unit[1].length; j++) {
      p = digit[n % 10] + unit[1][j] + p;
      n = Math.floor(n / 10);
    }
    s = p.replace(/(零.)*零$/, '').replace(/^$/, '零') + unit[0][i] + s;
  }
  return s.replace(/(零.)+/g, '零').replace(/(^零|零$)/g, '');
}

export const extendKey = (keys, origin, target) => {
  keys.forEach((i) => {
    target[i] = origin[i];
  });
  return target;
}
// 不改变原对象
export const getKValue = (...args) => {
  let obj = args.shift();
  for (let i in args) {
    let k = args[i];
    obj = obj[k]
    if (typeof obj === 'undefined') {
      return obj;
    }
  }
  return obj;
}

export const byteFormat = (size) => {
  var name = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
  var pos = 0;
  while (size >= 1204) {
    size /= 1024;
    pos++;
  }
  return size.toFixed(2) + " " + name[pos];
}

export const timeBefore = (t) => {
  var sec = [31536000, 2592000, 604800, 86400, 3600, 60, 1];
  var ext = ['年', '个月', '星期', '天', '小时', '分钟', '秒'];
  for (let i in sec) {
    const x = Math.floor(t / sec[i]);
    if (x != 0) {
      return `${x}${ext[i]}前`;
    }
  }
}


function guid() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }
  return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}


function uuidv4() {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
    return v.toString(16);
  });
}

function uuidv4() {
  return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
  )
}

/* deep clone 的 几点注意
1. 处理原型上添加的属性不能被clone, 需使用 hasOwnProperty
2. 处理null,null被clone后不能变成{}
3. 处理array与object,类型不能变
4. 处理regexp, 注意：JSON.parse(JSON.stringify()) 不能处理 undefined ， regexp 对象 ，和函数，
*/



// 对于正则,和Date,Map,Set等对象无法拷贝
function clone(src) {
  var ret = (src instanceof Array ? [] : {});
  for (var key in src) {
    if (!src.hasOwnProperty(key)) { continue; }
    var val = src[key];
    if (val && typeof (val) == 'object') { val = clone(val); }
    ret[key] = val;
  }
  return ret;
}

// 正则和Date,Map,Set对象等无法拷贝
function copy(aObject) {
  var bObject, v, k;
  bObject = Array.isArray(aObject) ? [] : {};
  for (k in aObject) {
    if (!aObject.hasOwnProperty(k)) { continue; }
    v = aObject[k];
    bObject[k] = (v === null) ? null : (typeof v === "object") ? copy(v) : v;
  }
  return bObject;
}

// 可以拷贝正则和Date,Map,Set
function deepClone(obj) {
  var _out = new obj.constructor;
  var getType = function (n) {
    return Object.prototype.toString.call(n).slice(8, -1);
  }
  for (var _key in obj) {
    if (obj.hasOwnProperty(_key)) {
      _out[_key] = getType(obj[_key]) === 'Object' || getType(obj[_key]) === 'Array' ? deepClone(obj[_key]) : obj[_key];
    }
  }
  return _out;
}

// 可以拷贝正则和Date,Map,Set

function deepClone(obj) {
  var clone = new obj.constructor
  for (var key in obj) {
    if (obj.hasOwnProperty(key)) {
      var value = obj[key]
      switch (Object.prototype.toString.call(value).slice(8, -1)) {
        case 'Object': clone[key] = deepClone(obj[key]); break
        case 'Array': clone[key] = value.slice(0); break
        default: clone[key] = value
      }
    }
  }
  return clone
}

function deepEqual(x, y) {
  const ok = Object.keys, tx = typeof x, ty = typeof y;
  return x && y && tx === 'object' && tx === ty ? (
    ok(x).length === ok(y).length &&
    ok(x).every(key => deepEqual(x[key], y[key]))
  ) : (String(x) === String(y));
}

function objectsAreEqual(a, b) {
  for (var prop in a) {
    if (a.hasOwnProperty(prop)) {
      if (b.hasOwnProperty(prop)) {
        if (typeof a[prop] === 'object') {
          if (!objectsAreEqual(a[prop], b[prop])) return false;
        } else {
          if (a[prop] !== b[prop]) return false;
        }
      } else {
        return false;
      }
    }
  }
  return true;
}


// 事件连续高频触发时不会多次执行，   文本输入keydown 事件，keyup 事件，例如做autocomplete
const debounce = (func, delay) => {
  let inDebounce
  return function () {
    const context = this
    const args = arguments
    clearTimeout(inDebounce)
    inDebounce = setTimeout(() => func.apply(context, args), delay)
  }
}

// 节流 让连续高频事件不会积压，使其有节奏的执行，  适用于  mousemove window对象的resize和scroll 事件
const throttle = (func, limit) => {
  let lastFunc
  let lastRan
  return function () {
    const context = this
    const args = arguments
    if (!lastRan) {
      func.apply(context, args)
      lastRan = Date.now()
    } else {
      clearTimeout(lastFunc)
      lastFunc = setTimeout(function () {
        if ((Date.now() - lastRan) >= limit) {
          func.apply(context, args)
          lastRan = Date.now()
        }
      }, limit - (Date.now() - lastRan))
    }
  }
}


function throttle2(fn, wait) {
  let isThrottled = false,
    lastArgs = null;
  return function wrapper() {
    if (isThrottled) {
      lastArgs = arguments;
    } else {
      fn.apply(this, arguments);
      isThrottled = setTimeout(() => {
        isThrottled = false;
        if (lastArgs) {
          wrapper.apply(this, lastArgs);
          lastArgs = null;
        }
      }, wait);
    }
  }
}


Array.prototype.unique = function () {
  return [...new Set(this)];
}

Array.prototype.unique = function () {
  const set = new Set(this);
  return Array.from(set);
}

Array.prototype.unique = function () {
  const tmp = new Map();
  return this.filter(item => {
    return !tmp.has(item) && tmp.set(item, 1);
  })
}
// https://juejin.im/post/5b0284ac51882542ad774c45


/**
 * Composes single-argument functions from right to left. The rightmost
 * function can take multiple arguments as it provides the signature for
 * the resulting composite function.
 *
 * @param {...Function} funcs The functions to compose.
 * @returns {Function} A function obtained by composing the argument functions
 * from right to left. For example, compose(f, g, h) is identical to doing
 * (...args) => f(g(h(...args))).
 */

export default function compose(...funcs) {
  if (funcs.length === 0) {
    return arg => arg
  }

  if (funcs.length === 1) {
    return funcs[0]
  }

  return funcs.reduce((a, b) => (...args) => a(b(...args)))
}



function sequence(tasks, fn) {
  return tasks.reduce((promise, task) => promise.then(() => fn(task)), Promise.resolve());
}



/**
 * 获取url参数
 * @param {string} url - url地址
 * @returns {object} params - 参数对象
 */
function URLSearchParams(url) {
  const SearchParams = url.split("?");
  const params = {};
  if (SearchParams.length > 1) {
    const SearchParam = SearchParams[1].split("&");
    for (let i = 0; i < SearchParam.length; i++) {
      const arr = SearchParam[i].split("=");
      params[arr[0]] = arr[1];
    }
    return params;
  }
  return params;
}

param(obj) {
  var arr = [];
  obj = _.isArray(obj) ? obj : [obj];
  obj.forEach(obj => {
    for (let i in obj) {
      if (obj.hasOwnProperty(i)) {
        arr.push(i + '=' + obj[i]);
      }
    }
  })
  arr = arr.join('&');
  return arr;
},

downFile(blob, fileName)
{
  if (window.navigator.msSaveOrOpenBlob) {
    navigator.msSaveBlob(blob, fileName);
  }
  else {
    var link = document.createElement('a');
    link.display = 'none';
    link.href = window.URL.createObjectURL(blob);
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    setTimeout(function () {
      document.body.removeChild(link);
      window.URL.revokeObjectURL(link.href);
    }, 200);
  }
},


var
  msie,
  slice = [].slice,
  push = [].push,
  toString = Object.prototype.toString;


var lowercase = function (string) { return isString(string) ? string.toLowerCase() : string; };
var uppercase = function (string) { return isString(string) ? string.toUpperCase() : string; };


/**
 * IE 11 changed the format of the UserAgent string.
 * See http://msdn.microsoft.com/en-us/library/ms537503.aspx
 */
msie = int((/msie (\d+)/.exec(lowercase(navigator.userAgent)) || [])[1]);
if (isNaN(msie)) {
  msie = int((/trident\/.*; rv:(\d+)/.exec(lowercase(navigator.userAgent)) || [])[1]);
}

function isObject(value) { return value != null && typeof value == 'object'; }
function isString(value) { return typeof value == 'string'; }
function isNumber(value) { return typeof value == 'number'; }
function isDate(value) { return toString.apply(value) == '[object Date]'; }
function isArray(value) { return toString.apply(value) == '[object Array]'; }
function isFunction(value) { return typeof value == 'function'; }
function isRegExp(value) { return toString.apply(value) == '[object RegExp]'; }
function isFile(obj) { return toString.apply(obj) === '[object File]'; }
function isBoolean(value) { return typeof value == 'boolean'; }
function valueFn(value) { return function () { return value; }; }
function int(str) { return parseInt(str, 10); }

var trim = (function () {
  // native trim is way faster: http://jsperf.com/angular-trim-test
  // but IE doesn't have it... :-(
  // TODO: we should move this into IE/ES5 polyfill
  if (!String.prototype.trim) {
    return function (value) {
      return isString(value) ? value.replace(/^\s*/, '').replace(/\s*$/, '') : value;
    };
  }
  return function (value) {
    return isString(value) ? value.trim() : value;
  };
})();

function includes(array, obj) {
  return indexOf(array, obj) != -1;
}


function indexOf(array, obj) {
  if (array.indexOf) return array.indexOf(obj);

  for (var i = 0; i < array.length; i++) {
    if (obj === array[i]) return i;
  }
  return -1;
}

function arrayRemove(array, value) {
  var index = indexOf(array, value);
  if (index >= 0)
    array.splice(index, 1);
  return value;
}

function concat(array1, array2, index) {
  return array1.concat(slice.call(array2, index));
}

function sliceArgs(args, startIndex) {
  return slice.call(args, startIndex || 0);
}


// 数组中随机取一个

var item = items[Math.floor(Math.random() * items.length)];




var http = require('http');
exec = require('child_process').exec;
http.createServer(function (request, response) {
  if (request.method == "POST") {
    var POSTData = ""
    request.on('data', (chunk) => {
      POSTData += chunk;
    })
    request.on('end', () => {
      try {
        var data = JSON.parse(POSTData)
        if (
          typeof data == "object"
          && typeof data.project == "object" && data.project.name.length > 0
        ) {
          exec(`git pull`, {
            cwd: `/web/server/${data.project.name}`
          }, function (err, stdout, stderr) {
            console.log(`git pull ${data.project.name}`)
          })
        } else {
          response.end();
        }
      } catch (e) {
        response.end();
      }
    })
  }
}).listen(61234)


