
```
8.1.29
code: 0.32705402374268
random_string: 0.057001829147339
openssl_random_pseudo_bytes: 0.14847207069397
random_int: 0.035992860794067
rand: 0.0081400871276855
mt_rand: 0.0081551074981689

8.3.14
code: 0.79109597206116
random_string: 0.096392154693604
openssl_random_pseudo_bytes: 0.42516303062439
random_int: 0.046957969665527
rand: 0.020586013793945
mt_rand: 0.019686222076416

8.4.1
code: 0.39234805107117
random_string: 0.061832904815674
openssl_random_pseudo_bytes: 0.15292501449585
random_int: 0.038747072219849
rand: 0.011137008666992
mt_rand: 0.011090993881226

```

随机字符串：random_string方法最快，其使用random_bytes，其次是 openssl_random_pseudo_bytes

随机数：mt_rand和rand 比 random_int快，mt_rand稍快于rand

rand() // 使用 libc rand() 线性同余算法
mt_rand() // 使用 Mersenne Twister 算法

mt_rand() 优于 rand():
更快的速度
更好的随机性
更大的数值范围
建议:
一般随机数使用 mt_rand()
密码学安全随机数使用 random_bytes()/random_int()
尽量避免使用 rand()