function validIdCard(str)
{
    if(typeof str !=='string'){
        return false;
    }
    const reg=/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([\d|X])$/;
    const province=[11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65,71,81,82,91];
    const power=[7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
    const refNumber =["1", "0", "X", "9", "8", "7", "6", "5", "4", "3", "2"];
    if(!reg.test(str)){
        return false;
    }
    if(province.findIndex(n=>n==str.substr(0,2))<0){
        return false;
    }
    const y = str.substr(6,4);
    const m = str.substr(10,2);
    const d = str.substr(12,2);
    const bir = new Date(`${y}/${m}/${d}`);
    if(bir.getFullYear()!=y){
        return false;
    }
    let birm = bir.getMonth()+1;
    birm = birm<9?`0${birm}`:birm;
    let bird = bir.getDate();
    bird = bird<9?`0${bird}`:bird;
    if(!(m==birm && d==bird)){
        return false;
    }
    if(+bir > +new Date() || +bir < (+new Date('1900/01/01'))){
        return false;
    }
    let sum=0;
    str.substr(0,17).split('').forEach((item,i)=>{
        sum += parseInt(item)*power[i];
    });
    let last = refNumber[sum%11];
    if(str.charAt(17)==last){
        return true;
    }
    return false;
}