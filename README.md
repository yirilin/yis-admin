# YisAdmin
Laravel+vue实现了JWT鉴权，动态路由用户管理、菜单管理、角色管理、权限管理、配置管理、行政区管理、操作历史、文章管理及一键生成模块代码（包括crud代码、VUE+JS代码）等功能；RESTful API风格。欢迎STAR!

## 启动步骤
```

/** 后台 */
cd YisAdmin
cd server
composer install
安装数据库,文件路径/database/yis_admin.sql
配置.env文件

/** 前台 */
cd YisAdmin
cd admin
npm install
npm run serve

/** 账户密码 */

测试用户名：admin
测试密码：123456

```

## 一键生成CRUD代码

#### 步骤
 - 第一步：设计好数据表
 - 第二步：选择查询字段
 - 第三步：生成代码
 - 第四步：将生成的route代码放到route/admin.php

#### demo图片

###### 文章管理
![article.png](https://raw.githubusercontent.com/yirilin/yis-admin/master/demoimg/article.png)

###### 菜单管理
![menu.png](https://raw.githubusercontent.com/yirilin/yis-admin/master/demoimg/menu.png)

###### 自动代码
![autoCode.png](https://raw.githubusercontent.com/yirilin/yis-admin/master/demoimg/autoCode.png)

###### 地区配置
![area.png](https://raw.githubusercontent.com/yirilin/yis-admin/master/demoimg/area.png)