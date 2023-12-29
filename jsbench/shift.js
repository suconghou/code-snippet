const n = 5e6;
const arr = new Array(n);
for (let i = 0; i++; i < n) {
    arr[i] = i;
}
const t1 = +new Date();
for (let i = 0; i < 500; i++) {
    arr.shift();
    // arr.pop();
}
const t2 = +new Date()

console.info(t2 - t1)


// shift 删除左侧 在大数据量下是比较慢的，因为要移动每一个
// pop 删除右侧，和数据量没有关系
