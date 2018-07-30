完整包安装 aaass11
前往官网下载页面(https://www.fastadmin.net/download.html)下载完整包解压到你的项目目录
添加虚拟主机并绑定到项目中的public目录
访问 http://www.yoursite.com/install.php 进行安装
命令行安装
强烈建议使用命令行安装，因为采用命令行安装的方式可以和FastAdmin随时保持更新同步。使用命令行安装请提前准备好Git、Node.js、Composer、Bower环境，我们为Windows下开发者准备了一个简单的视频安装教程( https://www.fastadmin.net/video/install.html )，可跟着教程一步一步安装。Linux下FastAdmin的安装请使用以下命令进行安装。

克隆FastAdmin到你本地
git clone https://gitee.com/karson/fastadmin.git
进入目录
cd fastadmin
下载前端插件依赖包
bower install
下载PHP依赖包
composer install
一键创建数据库并导入数据
php think install -u 数据库用户名 -p 数据库密码
添加虚拟主机并绑定到fastadmin/public目录


如果使用命令行安装则后台管理默认密码是123456
提示请先下载完整包覆盖后再安装，说明你是直接从仓库下载的代码，请从官网下载完整包覆盖后再进行安装
执行php think install时出现Access denied for user ...，请确保数据库服务器、用户名、密码配置正确
执行php think install时报不是内部或外部命令? 请将php.exe所在的目录路径加入到环境变量PATH中
如果提示当前权限不足，无法写入配置文件application/database.php，请检查database.php是否可读，还有可能是当前安装程序无法访问父目录，请检查PHP的open_basedir配置
如果提示找不到fastadmin.fa_admin表或表不存在，请检查你的MySQL是否开启了支持innodb。
如果在Linux环境中使用的是root账户，bower install执行出错，请尝试添加上--allow-root参数
如果访问后台右侧空白，请检查资源是否下载完整，可使用bower install多试两次或下载资源包覆盖