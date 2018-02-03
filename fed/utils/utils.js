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