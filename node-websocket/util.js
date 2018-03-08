module.exports = 
{
    findById(arr,id)
    {
        for(let i in arr)
        {
            const item = arr[i];
            if(item.id==id)
            {
                return i;
            }
        }
        return -1;
    },
    decodeFrame(data)
    {
        var dataIndex = 2; //数据索引，因为第一个字节和第二个字节肯定不为数据，所以初始值为2
        var fin = data[0] >> 7; //获取fin位，因为是第一位，所以8位二进制往后推7位
        var opcode = data[0] & parseInt(1111, 2); //获取第一个字节的opcode位，与00001111进行与运算
        var masked = data[1] >> 7; //获取masked位，因为是第一位，所以8位二进制往后推7位
        var payloadLength = data[1] & parseInt(1111111, 2); //获取数据长度，与01111111进行与运算
        var maskingKey,payloadData,remains = 0;
        //如果为126，则后面16位长的数据为数据长度，如果为127，则后面64位长的数据为数据长度
        if (payloadLength == 126) 
        {
            dataIndex += 2;
            payloadLength = data.readUInt16BE(2);
        } else if (payloadLength == 127) 
        {
            dataIndex += 8;
            payloadLength = data.readUInt32BE(2) + data.readUInt32BE(6);
        }
        //如果有掩码，则获取32位的二进制masking key，同时更新index
        if (masked) 
        {
            maskingKey = data.slice(dataIndex, dataIndex + 4);
            dataIndex += 4;
        }
        // 解析出来的数据
        payloadData = data.slice(dataIndex, dataIndex + payloadLength);
        // 剩余字节数
        remains = dataIndex + payloadLength - data.length;

        return {
            fin,
            opcode,
            masked,
            maskingKey,
            remains,
            payloadData
        };
    },
    encodeFrame(message)
    {
        message = String(message);
        var length = Buffer.byteLength(message);

        if (!length) return;

        //数据的起始位置，如果数据长度16位也无法描述，则用64位，即8字节，如果16位能描述则用2字节，否则用第二个字节描述
        var index = 2 + (length > 65535 ? 8 : (length > 125 ? 2 : 0));

        //定义buffer，长度为描述字节长度 + message长度
        var buffer = new Buffer(index + length);

        //第一个字节，fin位为1，opcode为1
        buffer[0] = 129;

        //因为是由服务端发至客户端，所以无需masked掩码
        if (length > 65535) 
        {
            buffer[1] = 127;

            //长度超过65535的则由8个字节表示，4个字节能表达的长度为4294967295，直接将前面4个字节置0
            buffer.writeUInt32BE(0, 2);
            buffer.writeUInt32BE(length, 6);
        }
        else if (length > 125) 
        {
            buffer[1] = 126;

            //长度超过125的话就由2个字节表示
            buffer.writeUInt16BE(length, 2);
        }
        else 
        {
            buffer[1] = length;
        }

        //写入正文
        buffer.write(message, index);

        return buffer;

    },
    parseData(receiver)
    {
        var result;

        if (receiver.maskingKey) 
        {
            result = new Buffer(receiver.payloadData.length);
            for (var i = 0; i < receiver.payloadData.length; i++) 
            {
                //对每个字节进行异或运算，masked是4个字节，所以%4，借此循环
                result[i] = receiver.payloadData[i] ^ receiver.maskingKey[i % 4];
            }
        }

        result = (result || receiver.payloadData).toString();

        return result;


    }

};