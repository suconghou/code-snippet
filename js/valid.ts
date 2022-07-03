
export interface objectStrMap<T> {
    [key: string]: T
}

export interface rule {
    [key: string]: string
}


export default class {

    private keys: Array<string> = [];
    constructor(private rules: objectStrMap<rule>) {
        this.keys = Object.keys(rules)
    }

    private clean(data: any) {
        for (const k of Object.keys(data)) {
            if (!this.keys.includes(k)) {
                delete data[k];
            }
        }
        return data;
    }

    private validItem(rule: rule, data: any, key: string) {
        if (typeof data === 'undefined' && !rule['require'] && !rule['required']) {
            return data
        }
        for (const [k, v] of Object.entries(rule)) {
            if (!this.validOne(k, v, data, key)) {
                throw new Error(v)
            }
        }
        return data
    }

    private validOne(k: string, v: string, item: any, key: string) {
        const arr = k.split('=').filter(i => i)
        if (arr.length == 2) {
            switch (arr[0]) {
                case 'minlength':
                    return typeof item === 'string' && item.length >= Number(arr[1]);
                case 'maxlength':
                    return typeof item === 'string' && item.length > 0 && item.length <= Number(arr[1]);
                case 'length':
                    const a = arr[1].split(',').filter(i => i)
                    if (a.length >= 2) {
                        return typeof item === 'string' && item.length && item.length >= Number(a[0]) && item.length <= Number(a[1])
                    }
                    return typeof item === 'string' && item.length && item.length === Number(arr[1])
                case 'int':
                    const b = arr[1].split(',').filter(i => i);
                    if (b.length >= 2) {
                        return Number.isSafeInteger(item) && item >= Number(b[0]) && item <= Number(b[1]);
                    }
                    return Number.isSafeInteger(item) && item === Number(arr[1])
                case 'number':
                    const c = arr[1].split(',').filter(i => i);
                    if (c.length >= 2) {
                        return Number.isFinite(item) && item >= Number(c[0]) && item <= Number(c[1])
                    }
                    return Number.isFinite(item) && item === Number(arr[1])
                case 'eq':
                    return typeof item === 'string' && item.trim() === arr[1].trim()
                case 'eqs':
                    return typeof item === 'string' && item.trim().toLowerCase() === arr[1].trim().toLowerCase()
                case 'set':
                    return arr[1].split(',').filter(i => i).includes(item)
                default:
                    return new RegExp(k).test(item);
            }
        } else {
            switch (k) {
                case 'required':
                    return item && item !== 0;
                case 'require':
                    return item === 0 || item;
                case 'default':
                    return true;
                case 'int':
                    return Number.isSafeInteger(item);
                case 'number':
                    return Number.isFinite(item);
                case 'string':
                    return typeof item === 'string';
                case 'bool':
                    return typeof item === 'boolean';
                case 'array':
                    return Array.isArray(item);
                case 'object':
                    return typeof item === 'object';
                case 'scalar':
                    return ['string', 'boolean', 'number'].includes(typeof item)
                case 'email':
                    return /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(item)
                case 'password':
                    return /^(?=^.{8,}$)(?=.*\d)(?=.*\W+)(?=.*[A-Z])(?=.*[a-z])(?!.*\n).*$/.test(item)
                case 'phone':
                    return /^1\d{10}$/.test(item)
                case 'url':
                    return /^[a-zA-z]+:\/\/[^\s]*$/.test(item)
                case 'ip':
                    return /^((2[0-4]\d|25[0-5]|[01]?\d\d?)\.){3}(2[0-4]\d|25[0-5]|[01]?\d\d?)$/.test(item)
                case 'idcard':
                    return /^\d{15}(\d\d[0-9xX])?$/.test(item)
                default:
                    return new RegExp(k).test(item)
            }
        }
    }

    valid(data: any) {
        data = this.clean(data)
        for (const k of this.keys) {
            data[k] = this.validItem(this.rules[k], data[k], k)
        }
        return data;
    }



}


