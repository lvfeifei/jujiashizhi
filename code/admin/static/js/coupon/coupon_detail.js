
export default {
    data() {
        return {
            coupon_id: 0,
            detail: '',
            token: '',
            goods_list: [],
        }
    },

    /**
     * 
     */
    mounted() {
        var that = this;
        
        if (that.$route.query.coupon_id) {
            that.coupon_id = that.$route.query.coupon_id;
            that.get_detail();
        }
    },

    /**
     * 方法
     */
    methods: {
        /**
         * 获取详情
         */
        get_detail() {
            var that = this;
            that.axios.post("/Coupon/show_edit", {
                token: that.token,
                coupon_id: that.coupon_id
            }, {
                emulateJSON: true
            }).then(res => {
                var data = res;
                if (data) {
                    that.detail = data;
                    if (data.range == 2) {
                        that.goods_list = data.goods_list;
                    }
                }
            });
        },

        /**
         * 下载图片
         * @param {*} imgsrc 
         * @param {*} name 
         */
        downloadIamge(imgsrc, name) {//下载图片地址和图片名
            var image = new Image();
            // 解决跨域 Canvas 污染问题
            image.setAttribute("crossOrigin", "anonymous");
            image.onload = function () {
                var canvas = document.createElement("canvas");
                canvas.width = image.width;
                canvas.height = image.height;
                var context = canvas.getContext("2d");
                context.drawImage(image, 0, 0, image.width, image.height);
                var url = canvas.toDataURL("image/png"); //得到图片的base64编码数据

                var a = document.createElement("a"); // 生成一个a元素
                var event = new MouseEvent("click"); // 创建一个单击事件
                a.download = name || "photo"; // 设置图片名称
                a.href = url; // 将生成的URL设置为a.href属性
                a.dispatchEvent(event); // 触发a的单击事件
            };
            image.src = imgsrc;
        },
        downs() {
            this.downloadIamge(this.detail.code_url, this.detail.name)
        }
    }
}