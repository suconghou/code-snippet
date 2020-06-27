<?php

/**
 * 
 * 
ftyp (24 bytes)
moov (644 bytes)
sidx (236 bytes)
moof (1816 bytes)
mdat (160194 bytes)
moof (1816 bytes)
mdat (159649 bytes)
moof (1816 bytes)
mdat (159874 bytes)
moof (1816 bytes)
mdat (159595 bytes)
moof (1816 bytes)
mdat (159878 bytes)
moof (1816 bytes)
mdat (159986 bytes)
moof (1816 bytes)
mdat (159591 bytes)
moof (1816 bytes)
mdat (159778 bytes)
moof (1816 bytes)
mdat (159701 bytes)
moof (1816 bytes)
mdat (159727 bytes)
moof (1816 bytes)
mdat (159990 bytes)
moof (1816 bytes)
mdat (159586 bytes)
moof (1816 bytes)
mdat (159737 bytes)
moof (1816 bytes)
mdat (159811 bytes)
moof (1816 bytes)
mdat (159974 bytes)
moof (1816 bytes)
mdat (159582 bytes)
moof (2828 bytes)
mdat (126902 bytes)
 */

$offset = 668;

$maxlen = 236;

// 包含 $offset  668-903 共 236字节

// md5 e2116e096c1ee686b13b453a4a1d513f

$f = "/Users/admin/Downloads/140.mp4";

$data = file_get_contents($f, false, null, $offset, $maxlen);


var_dump(strlen($data), md5_file($f),  md5($data));



class parser
{

    private $index = 0;
    private $data = '';

    function __construct(string $data)
    {
        $this->data = $data;
    }

    function read(int $n)
    {
        $r = substr($this->data, $this->index, $n);
        $this->index += $n;
        return $r;
    }

    function readInt(int $n)
    {
        $s = $this->read($n);
        return base_convert(bin2hex($s), 16, 10);
    }

    function parse()
    {
        $this->read(8);
        $version = $this->readInt(1);
        $flags = $this->readInt(3);
        $referenceId = $this->readInt(4);
        $timeScale = $this->readInt(4);
        if ($version == 0) {
            $earliest_presentation_time = $this->readInt(4);
            $first_offset = $this->readInt(4);
        } else {
            $earliest_presentation_time = $this->readInt(8);
            $first_offset = $this->readInt(8);
        }
        $reserved = $this->read(2);
        $reference_count = $this->readInt(2);

        $references = [];
        $offset = $first_offset;
        $time = $earliest_presentation_time;
        for ($i = 0; $i < $reference_count; $i++) {
            // 由于reference_type的值都是0,最最终结果无影响,所以下面可以直接将$t转为10进制
            $reference_type = 0;
            $reference_size = $this->readInt(4);

            $subsegment_duration = $this->readInt(4);

            // 下面是 starts_with_SAP, SAP_type, SAP_delta_time 没用到,这里忽略掉
            $this->read(4);

            $startRange = $offset;
            $endRange = $offset + $reference_size - 1;

            $references[] = [
                "reference_type" => $reference_type,
                "reference_size" => $reference_size,
                "subsegment_duration" => $subsegment_duration,
                "durationSec" => $subsegment_duration / $timeScale,
                "startTimeSec" => $time / $timeScale,
                "startRange" => $startRange,
                "endRange" => $endRange,
            ];
            $offset += $reference_size;
            $time += $subsegment_duration;
        }

        $info =
            [
                "version" => $version,
                "flag" => $flags,
                "referenceId" => $referenceId,
                "timeScale" => $timeScale,
                "earliest_presentation_time" => $earliest_presentation_time,
                "first_offset" => $first_offset,
                "reference_count" => $reference_count,
                "references" => $references,
            ];
        return $info;
    }
}

$parser = new parser($data);

var_dump($parser->parse());
