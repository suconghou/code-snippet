/**
 * 
 * in most case foreachloop is better
 * 
 */

const t = 1e4;
const obj = {
};

for (let i = 0; i < 200; i++) {
    obj[i] = Math.random();
}

function forinloop() {
    const t1 = +new Date();
    for (let i = 0; i < t; i++) {
        for (let j in obj) {
            const v = obj[j];
        }
    }
    const t2 = +new Date() - t1;
    console.info(`forinloop: ${t2}`);
    return t2
}

function foreachloop() {
    const t1 = +new Date();
    for (let i = 0; i < t; i++) {
        Object.keys(obj).forEach(k => {
            const v = obj[k];
        });
    }
    const t2 = +new Date() - t1
    console.info(`foreachloop: ${t2}`);
    return t2;

}

const m = forinloop();
const n = foreachloop();

console.info(m > n ? 'foreachloop better' : 'forinloop better');