<?php



class EBMLParser
{
    public static $map = [
        '1c53bb6b' => ['Cues', 'master'],  // lvl. 1
        'bb' => ['CuePoint', 'master'],      // lvl. 2
        'b3' => ['CueTime', 'uinteger'],    // lvl. 3
        'b7' => ['CueTrackPositions', 'master'],      // lvl. 3
        'f7' => ['CueTrack', 'uinteger'],    // lvl. 4
        'f1' => ['CueClusterPosition', 'uinteger'],    // lvl. 4
    ];

    static function info(string $id)
    {
        if (isset(self::$map[$id])) {
            return self::$map[$id];
        }
        return ['', ''];
    }
}

class EBMLParserBuffer
{
    function __construct(string $data)
    {
        $this->data = $data;
        $this->index = 0;
    }

    function read(int $n)
    {
        $r = substr($this->data, $this->index, $n);
        $this->index += $n;
        return $r;
    }

    function rewind(int $n)
    {
        $this->index -= $n;
        return $this;
    }


    function readVint()
    {
        $s = $this->read(1);
        $w = self::vIntWidth($s) + 1;
        $id = $this->rewind(1)->read($w);
        $s = $this->read(1);
        $w = self::vIntWidth($s) + 1;
        $len = $this->rewind(1)->read($w);
        $lenNum = self::vIntNum($len, $w);
        $data = $this->read($lenNum);
        return [
            "id" => bin2hex($id),
            "length" => $lenNum,
            "data" => $data,
        ];
    }

    function parse()
    {
        if ($this->index < strlen($this->data)) {
            return $this->readVint();
        }
    }

    // 传入一个字节8位, 判断前多少个bit是0, 返回值可能为 0 - 7
    private static function vIntWidth($byte)
    {
        $byteNum = base_convert(bin2hex($byte), 16, 10);
        for ($width = 0; $width < 8; $width++) {
            if ($byteNum >= 2 ** (7 - $width)) {
                break;
            }
        }
        return $width;
    }

    // 传入若干个字节 最高位1置为0后转为十进制数
    // 我们已知高位前w-1位都是0,只需要将前高位w字节都置为0, $w 取值有 1-8
    private static function vIntNum($byte, $w)
    {
        return base_convert(bin2hex($byte), 16, 10) & (2 ** (8 - $w) - 1);
    }
}


class EBMLParserElement
{
    public $id;
    private $type;

    private $children = [];
    private $buffer;


    function __construct(string $buffer)
    {
        $this->buffer = new EBMLParserBuffer($buffer);
    }

    function parseElements()
    {
        while ($item = $this->buffer->parse()) {
            ['id' => $id, 'data' => $data] = $item;
            $this->children[] = $this->parseElement($id, $data);
        }
        return $this->children;
    }

    function parseElement(string $id, string $data)
    {
        $element = new EBMLParserElement($data);
        $element->id = $id;
        [$element->name, $element->type] = EBMLParser::info($id);
        if ($element->type == 'master') {
            $element->parseElements();
        } else {
            $element->value = base_convert(bin2hex($data), 16, 10);
        }
        return $element;
    }

    function __debugInfo()
    {
        $data = [
            'id' => $this->id,
            'name' =>  $this->name,
            'type' =>  $this->type,
        ];
        if ($this->children) {
            $data['children'] = $this->children;
        } else {
            $data['value'] = $this->value;
        }
        return $data;
    }
}

class parser
{
    private $index = 0;

    function __construct(string $data)
    {
        $this->data = $data;
    }

    function parse()
    {
        $a = new EBMLParserElement($this->data);
        $data = $a->parseElements();
        print_r($data);
    }
}



$f = "/Users/admin/Downloads/249.webm";

$offset = 259;
$maxlen = 286;

$data = file_get_contents($f, false, null, $offset, $maxlen);


var_dump(strlen($data), md5_file($f),  md5($data));

$parser = new parser($data);

var_dump($parser->parse());
