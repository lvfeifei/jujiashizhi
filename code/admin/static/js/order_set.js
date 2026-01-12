export default {
    data() {
        return {
            automatic_cancel_pay_time: 0,
            automatic_delivery_time:0
        };
    },

    //进入页面加载
    mounted: function() {
        var that = this;

        //返利api
        that.getOrderSet();
    },

    methods: {
        //请求返利设置
        getOrderSet: function() {
            var that = this;
            //返利内容
            that.axios.post("/Config/getOrderSet", {
                token: that.token
            }, {
                emulateJSON: true
            }).then(
                function(res) {
                    // 处理成功的结果
                    that.automatic_cancel_pay_time = res.automatic_cancel_pay_time;
                    that.automatic_delivery_time = res.automatic_delivery_time;
                },
                function() {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //保存
        save: function() {
            var that = this;
            //请求的参数
            var formData = {};
            formData.token = that.token;
            formData.automatic_cancel_pay_time = that.automatic_cancel_pay_time;
            formData.automatic_delivery_time = that.automatic_delivery_time;
            //设置
            that.axios.post("/Config/addOrderSet", formData, {
                emulateJSON: true
            }).then(
                function(res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${ '保存成功' }`
                    });
                },
                function() {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //返回
        cancel: function() {
            var that = this;
            that.$router.push('/nav1/shop_list');
        },
    }
}