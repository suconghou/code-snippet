
## WebGL与着色器(shader)

shader有自己的语言，叫OpenGL Shader Language，简称GLSL；WebGL用的着色器语言正是来自OpenGL ES 的着色器语言：GLSL ES；两者并没有多大差别，都可为WebGL三维图形开发提供强大的渲染，只不过GLSL适应于电脑客户端的开发，在功能上要强大许多，而GLSL ES由GLSL精简而来。所以WebGL的开发可以这样简单概括：HTML5 + JavaScript + OpengGL ES 2.0 + shader

## 1.着色器简介

着色器语言(GLSL ES)由OpenGL的着色器语言GLSL简化而来， 主要是为了适应目前的移动端手持设备，例如智能手机和平板电脑，由于这些手持设备受限于硬件条件，其性能远不如电脑。这意味着对这些手持设备硬件的利用和开销就要恰到好处，故由此催生了GLSL ES，是为了降低硬件的功耗和性能开销。具体的着色器技术文档我们可以参考官方网址：https://www.khronos.org/registry/OpenGL-Refpages/ 

## 2.着色器类型

WebGL着色器只用到众多着色器中的两种：顶点着色器(vertex shader)和片段着色器(fragment shader)。对于这两个着色器，在这里并不去过多的做详述，可以先这样理解：顶点着色器负责接收来自javaScript程序的顶点数据和颜色信息，经过一系列操作（像剪切，光栅化等，这一系列操作可以不用去深入理解），再将颜色信息传至片段着色器，最后生成图像呈现在我们面前。下面就对这两个着色器的代码做一个初步了解：

(1) 顶点着色器


```
attribute vec4 vPosition;//收js程序的顶点信息的变量
attribute vec4 vColor;//接受js程序的顶点颜色信息的变量
uniform mat4 vMvpMatrix;//接收视图变换矩阵
varying vec4 fColor;//顶点着色器与片段着色器传颜色信息的变量
void main(){
   gl_Position = vMvpMatrix * vPosition;//图像所有顶点位置
}
```

下面的任务可由顶点着色器完成： 
顶点变换 
纹理坐标生成和变换 
光照的计算 
颜色材料的应用

(2) 片段着色器

```
precision mediump float;//精度要求为中精度
varying vec4 fColor;//与顶点着色器中fColor一致，为了接收颜色信息
void main() {
  gl_FragColor = fColor;//给片元设置颜色
}
```

下面的任务可由片段着色器完成： 
插值操作 
纹理访问和应用 
雾化效果 
颜色总和

看到这里是不是觉得着色器语言似曾相识？学过C或C++的同学都能看出来了吧，着色器语言与C是多么的相像，但它们之间还是存在差异的，这里不赘述。所以如果你熟悉C，那么就会比较好理解GLSL ES。下面进行着色器一些变量的定义和语法讲述。



