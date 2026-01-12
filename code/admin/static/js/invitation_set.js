export default {
    data() {
        return {
            invite_rebate_switch: '1',
            invite_rebate_status:'1',
            invite_rebate_day:0,

            invite_protect_status:'1',
            invite_protect_day:0,

            invite_commission_rate:0,

            discount_rate:0,

            auto_balance_type:'1',
            auto_balance_day:0
        };
    },

    //进入页面加载
    mounted: function () {
        var that = this;

        //返利api
        that.getConfigDetil();
    },

    methods: {
        //请求返利设置
        getConfigDetil: function () {
            var that = this;
            //请求邀请者列表
            that.axios.post("/Config/getRebateSet", {
                token:that.token
            }, {emulateJSON: true}).then(
                function (res) {
                    //开关
                    that.invite_rebate_switch = res.invite_rebate_switch;
                    //有效期
                    that.invite_rebate_status = res.invite_rebate_status;
                    that.invite_rebate_day = res.invite_rebate_day;
                    //保护期
                    that.invite_protect_status = res.invite_protect_status;
                    that.invite_protect_day = res.invite_protect_day;
                    //佣金
                    that.invite_commission_rate = res.invite_commission_rate;
                    //优惠
                    that.discount_rate = res.discount_rate;
                    //结算
                    that.auto_balance_type = res.auto_balance_type;
                    that.auto_balance_day = res.auto_balance_day;
                }, function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //保存
        save:function () {
            var that = this;

            //请求的参数
            var formData = {};
            formData.token = that.token;
            //开关
            formData.invite_rebate_switch = that.invite_rebate_switch;
            //有效期
            formData.invite_rebate_status = that.invite_rebate_status;
            formData.invite_rebate_day = that.invite_rebate_day;
            //保护期
            formData.invite_protect_status = that.invite_protect_status;
            formData.invite_protect_day = that.invite_protect_day;
            //佣金
            formData.invite_commission_rate =  that.invite_commission_rate;
            //优惠
            formData.discount_rate = that.discount_rate;
            //结算
            formData.auto_balance_type = that.auto_balance_type;
            formData.auto_balance_day = that.auto_balance_day;
            //设置
            that.axios.post("/Config/SetRebate", formData, {emulateJSON: true}).then(
                function (res) {
                    // 处理失败的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${ '保存成功' }`
                    });
                }, function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //返回
        cancel: function () {
            var that = this;
            that.$router.push('/nav1/shop_list');
        },
    }
}