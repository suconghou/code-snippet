import hashlib


class parser():
    byteorder = "big"

    def __init__(self, data):
        self.data = data
        self.index = 0

    def read(self, n):
        r = self.data[self.index:self.index+n]
        self.index += n
        return r

    def readInt(self, n):
        r = self.read(n)
        return int.from_bytes(r, self.byteorder)

    def parse(self):
        self.read(8)
        version = self.readInt(1)
        flags = self.readInt(3)
        referenceId = self.readInt(4)
        timeScale = self.readInt(4)
        if (version == 0):
            earliest_presentation_time = self.readInt(4)
            first_offset = self.readInt(4)
        else:
            earliest_presentation_time = self.readInt(8)
            first_offset = self.readInt(8)
        reserved = self.read(2)
        reference_count = self.readInt(2)

        references = []
        offset = first_offset
        time = earliest_presentation_time
        for i in range(reference_count):
            # 由于reference_type的值都是0,最最终结果无影响,所以下面可以直接将$t转为10进制
            reference_type = 0
            reference_size = self.readInt(4)

            subsegment_duration = self.readInt(4)

            # 下面是 starts_with_SAP, SAP_type, SAP_delta_time 没用到,这里忽略掉
            self.read(4)

            startRange = offset
            endRange = offset + reference_size - 1

            references.append({
                "reference_type": reference_type,
                "reference_size": reference_size,
                "subsegment_duration": subsegment_duration,
                "durationSec": subsegment_duration / timeScale,
                "startTimeSec": time / timeScale,
                "startRange": startRange,
                "endRange": endRange,
            })
            offset += reference_size
            time += subsegment_duration

        info = {
            "version": version,
            "flag": flags,
            "referenceId": referenceId,
            "timeScale": timeScale,
            "earliest_presentation_time": earliest_presentation_time,
            "first_offset": first_offset,
            "reference_count": reference_count,
            "references": references,
        }
        return info


f = "/Users/admin/Downloads/140.mp4"


with open(f, 'rb') as file:
    data1 = file.read()
    data = data1[668:904]
    md5 = hashlib.md5()
    md5.update(data)
    print(md5.hexdigest())
    # python 和 go 一样, 包含前者,不包含后者
    print("parse len", len(data))
    p = parser(data)
    print(p.parse())
