const util = require('util');
const fs = require('fs');
const path = require('path');

const access = util.promisify(fs.access);


export const utils = {
    resolveLookupPaths(pathstr,file){
        const arr = [];
        const tmp=[];
        pathstr.split(path.sep).forEach(item=>
        {
            tmp.push(item);
            arr.push(path.resolve(path.join(path.sep,...tmp,file)));
        });
        return arr.reverse();
    },
    async tryfile(paths){
        for( let i in paths){
            const p = paths[i];
            const err = await access(p, fs.constants.R_OK);
            if(!err){
                console.info('ok',p);
                return p;
            } else {
                console.info('not ok ',p)
            }
        }
    },
    async readfile(paths){
        const f = await this.tryfile(paths);
        if(f){
            const obj = require(f);
            return obj;
        }
    }
}

( async function(){
    const a=  await utils.readfile(utils.resolveLookupPaths(process.cwd(),'index.js'));
    console.info(a)
})()


