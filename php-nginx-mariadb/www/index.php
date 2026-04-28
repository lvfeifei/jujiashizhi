<?php
// 测试 PHP 环境
echo "<h1>PHP 环境正常</h1>";
echo "PHP 版本：" . phpversion() . "<br>";

// 测试数据库连接
#try {
#    $pdo = new PDO(
#        'mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DATABASE'),
#        getenv('MYSQL_USER'),
#        getenv('MYSQL_PASSWORD')
#    );
#    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
#    echo "<h2>数据库连接成功 ✅</h2>";
#} catch (PDOException $e) {
#    echo "<h2 style='color: red;'>数据库连接失败 ❌</h2>";
#    echo "错误信息：" . $e->getMessage();
#}
?>
