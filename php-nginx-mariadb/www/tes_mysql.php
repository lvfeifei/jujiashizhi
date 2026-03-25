"<?php
$host = 'mariadb';  // Docker 服务名（同一网络）
$port = 3306;     // 替换为实际的 MySQL 主机端口
$user = 'jujiashizhi';
$password = '17188c5fdee5692e';
$dbname = 'jujiashizhi';

// 关键：新增端口参数
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die('连接失败: ' . $conn->connect_error);
}
echo '连接成功！';
$conn->close()
?>
