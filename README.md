# :tada: Laravue
Laravel+vue实现了JWT鉴权，动态路由用户管理、菜单管理、角色管理、权限管理、配置管理、行政区管理、操作历史、文章管理及一键生成模块代码（包括crud代码、VUE+JS代码）等功能；RESTful API风格。欢迎STAR!

## :cake: 启动步骤
```

/** 后台 */
cd YisAdmin
cd server
composer install
安装数据库(/database/yis_admin.sql)
配置数据库（.env）

/** 前台 */
cd YisAdmin
cd admin
npm install
npm run serve

/** 账户密码 */

测试用户名：admin
测试密码：123456

```

## :tada: 一键生成CRUD代码

#### :four_leaf_clover: 步骤
 - 第一步：设计好数据表
 - 第二步：设计查询字段
 - 第三步：生成代码
 - 第四步：将生成的route代码放到route/admin.php