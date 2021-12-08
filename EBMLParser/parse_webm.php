<?php



class EBMLParser
{
    public static $map = [
        '1a45dfa3' => ['EBML', 'm'],
        '18538067' => ['Segment', 'm'],
        '1f43b675' => ['Cluster', 'm'],
        '114d9b74' => ['SeekHead', 'm'],
        '4286' => ['EBMLVersion', 'u'],
        'a3' => ['SimpleBlock', 'b'],
        '1c53bb6b' => ['Cues', 'm'],  // lvl. 1
        'bb' => ['CuePoint', 'm'],      // lvl. 2
        'b3' => ['CueTime', 'u'],    // lvl. 3
        'b7' => ['CueTrackPositions', 'm'],      // lvl. 3
        'f7' => ['CueTrack', 'u'],    // lvl. 4
        'f1' => ['CueClusterPosition', 'u'],    // lvl. 4
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
    public string $data;
    public int $index;
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


    /**
     * vint 解析
     * 先读一字节,判断此字节的前多少bit位是0 => VINT_WIDTH
     * VINT_WIDTH+1表示对应的vint占用的字节数目 => 即得出id
     * 下面分析length 和 value 
     * 再读一字节
     * 
     */
    function readVint()
    {
        $s = $this->read(1);
        $w = self::vIntWidth($s) + 1;
        $id = $this->rewind(1)->read($w);
        $s = $this->read(1);
        $w = self::vIntWidth($s) + 1;
        $len = $this->rewind(1)->read($w);
        $lenNum = self::vIntNum($len);
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
    private static function vIntNum($byte)
    {
        $x = base_convert(bin2hex($byte), 16, 10);
        $k = 0;
        if ($x >> ($k ^ 32))  $k = $k ^ 32;
        if ($x >> ($k ^ 16)) $k = $k ^ 16;
        if ($x >> ($k ^ 8)) $k = $k ^ 8;
        if ($x >> ($k ^ 4)) $k = $k ^ 4;
        if ($x >> ($k ^ 2)) $k = $k ^ 2;
        if ($x >> ($k ^ 1)) $k = $k ^ 1;
        $x = $x ^ (1 << $k);
        return $x;
    }
}


class EBMLParserElement
{
    private string $id;
    private string $type;

    private array $children = [];
    private EBMLParserBuffer $buffer;


    function __construct(string $buffer)
    {
        $this->buffer = new EBMLParserBuffer($buffer);
    }

    function parseElements()
    {
        while ($item = $this->buffer->parse()) {
            ['id' => $id, 'length' => $length, 'data' => $data] = $item;
            $this->children[] = $this->parseElement($id, $length, $data);
        }
        return $this->children;
    }

    function parseElement(string $id, int $length, string $data)
    {
        $element = new EBMLParserElement($data);
        $element->id = $id;
        $element->length = $length;
        [$element->name, $element->type] = EBMLParser::info($id);
        if ($element->type == 'm') {
            $element->parseElements();
        } else if ($element->type == 'b') {
            $element->value = bin2hex($data);
        } else {
            $element->value = base_convert(bin2hex($data), 16, 10);
        }
        return $element;
    }

    function __debugInfo()
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'length' => $this->length,
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

    function __construct(string $data)
    {
        $this->data = $data;
    }

    function parse()
    {
        $a = new EBMLParserElement($this->data);
        $data = $a->parseElements();
        return $data;
    }
}



$f = "/tmp/243.webm";

$offset = 219;
$maxlen = 1009;

// $data = file_get_contents($f, false, null, $offset, $maxlen);

$data = file_get_contents($f);

// var_dump(strlen($data),  md5($data));

$parser = new parser($data);
$res = $parser->parse();
print_r($res);
