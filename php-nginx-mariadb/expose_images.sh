#!/bin/bash
# 导出 Compose 项目的所有镜像
COMPOSE_FILE="docker-compose.yml"  # 你的 Compose 文件路径
OUTPUT_DIR="./compose-images"      # 导出文件存放目录

# 创建输出目录
mkdir -p $OUTPUT_DIR

# 提取 Compose 中的所有镜像名
IMAGES=$(grep -E 'image:' $COMPOSE_FILE | awk '{print $2}' | grep -v '#' | sort | uniq)

# 批量导出
for IMAGE in $IMAGES; do
    # 替换镜像名中的特殊字符为下划线，作为文件名
    FILE_NAME=$(echo $IMAGE | tr '/:' '_')
    echo "导出镜像：$IMAGE -> $OUTPUT_DIR/$FILE_NAME.tar"
    docker save -o $OUTPUT_DIR/$FILE_NAME.tar $IMAGE
done

echo "所有镜像导出完成，存放路径：$OUTPUT_DIR"
