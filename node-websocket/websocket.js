const EventEmitter = require("events").EventEmitter;
const utiljs = require('./util.js');
var index=0;
var connections=[];

function WebSocket(socket)
{
    this.id = ++index;
    this.socket = socket;
}

WebSocket.addConnection=function(instance)
{
    connections.push(instance);
}

WebSocket.removeConnection=function(connectionId)
{
    var index=utiljs.findById(connections,connectionId);
    if(index>=0)
    {
        connections.splice(index,1);
        return true;
    }
    return false;
}

WebSocket.removeAllConnection=function()
{
    connections=[];
}

WebSocket.getAllConnection=function()
{
    return connections;
}

WebSocket.getConnection=function(connectionId)
{
    for(let i in connections)
    {
        const item = connections[i];
        if(item.id==connectionId)
        {
            return item;
        }
    }
    return null;
}

WebSocket.brocast=function(data)
{
    connections.forEach(function(instance)
    {
        instance.send(data);
    });
}

WebSocket.prototype.send=function(data)
{
    this.socket.send();
}


WebSocket.prototype.sendPing = function() 
{
    this.socket.write(new Buffer(['0x89', '0x0']));
};
WebSocket.prototype.sendPong = function() 
{
    this.socket.write(new Buffer(['0x8A', '0x0']));
};


WebSocket.prototype.onOpen=function(callback)
{

};


WebSocket.prototype.onClose=function(callback)
{

};


WebSocket.prototype.onError=function(callback)
{

};

WebSocket.prototype.onMessage=function(callback)
{
    var _this = this;
    this.socket.on('data',function(data)
    {
        var receiver = _this.receiver;

        if (!receiver) 
        {
            receiver = utiljs.decodeFrame(data);

            if (receiver.opcode === 8) 
            { // 关闭码
                _this.close(new Error("client closed"));
                return;
            }
            else if (receiver.opcode === 9) 
            { // ping码
                _this.sendPong();
                return;
            }
            else if (receiver.opcode === 10) 
            { // pong码
                _this.pingTimes = 0;
                return;
            }

            _this.receiver = receiver;

        } 
        else 
        {
            // 将新来的数据跟此前的数据合并
            receiver.payloadData = Buffer.concat(
                [receiver.payloadData, data],
                receiver.payloadData.length + data.length
            );

            // 更新数据剩余数
            receiver.remains -= data.length;
        }

        // 如果无剩余数据，则将receiver置为空
        if (receiver.remains <= 0) 
        {
            receiver = utiljs.parseData(_this.receiver);
            setTimeout(()=>
            {
                callback(receiver);
            })
            _this.receiver = null;
        }

        console.info(data,data.toString());
    });
    return this;
}

module.exports = WebSocket