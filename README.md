# **QR Hotel后台管理系统**

本系统主要用于QR Hotel平台入驻酒店的管理及酒店对自家业务数据的管理。  
本系统基于FastAdmin、ThinkPHP5、Bootstrap框架开发。

## 技术需求

* 开发语言 : php 7.2 及以上
* 数据库 : MySQL 5.6 及以上（MariaDB也可以）
* php扩展 ：GD、mysqli、mbstring、curl

## 开发环境配置

* 本机开发环境：
    * XAMPP（Apache + php + MySQL 整合安装包）
        * https://www.apachefriends.org/
        * 安装完毕后设置
            1. 克隆本项目代码到本地文件夹
            2. 通过xampp控制面板打开·httpd.conf，将下列地址指向本地代码的public文件夹
                ```
                DocumentRoot "/xampp/htdocs"
                <Directory "/xampp/htdocs">
                ```
            3. 启动MySQL、Apache
            4. 通过xampp控制面板打开phpmyadmin
            5. 导入qrhotel_对应版本号.sql
            6. 创建数据库用户，赋予全部权限
            7. 编辑本地代码/application/database.php
                * 修改数据库地址为localhost
                * 修改用户名密码为第6步创建的用户名密码
            8. 访问下列地址出现登录页面说明配置成功
                * http://localhost/

* 开发工具推荐：
    * PhpStorm
        * https://www.jetbrains.com/phpstorm/
    * Visual Studio Code
        * https://code.visualstudio.com/
        * 安装php开发插件

## 参考资料

* FastAdmin文档：
    * https://doc.fastadmin.net

* ThinkPHP5.0文档：
    * https://www.kancloud.cn/manual/thinkphp5/
