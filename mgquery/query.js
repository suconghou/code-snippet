

function NewQuery(init)
{

    return new Proxy({},{
        get(obj,key){
            return init(key)
        }
    })

}

// set get 

// cache 


// exaple 

// fetch 

var q1 = NewQuery(function(key)
{
    return function(...param){
        return fetch(key,...param)
    }
});


q1.userinfo({body: {a:2}}).then(res=>console.info(res));