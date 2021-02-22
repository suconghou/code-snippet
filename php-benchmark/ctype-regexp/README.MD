

如果要检测是不是整数数据类型 , 使用 is_int() 同理检测浮点数使用 is_float()
此两函数都是严格检测





preg_match took 0.024676084518433 seconds.
ctype_digit took 0.0065488815307617 seconds.
filter_var took 0.012675046920776 seconds.



ctype_digit 快于 filter_var 快于 preg_match