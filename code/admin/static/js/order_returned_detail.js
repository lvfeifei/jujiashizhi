export default {
    data() {
        return {
            status: '',
            order_id: '',
            order_status: 1,
            order_data: {},
            // 拒绝退货弹窗
            refuse_return_dia: false,
            remarks_dia: false,
            // 备注
            remarks_etxt: '',
            refuse_return_text: ''
        }
    },
    //进入页面加载
    mounted: function () {
        var that = this;

        var query = that.$route.query;
        if (query.order_id) {
            that.order_id = query.order_id;
            that.get_detail()
        }


    },

    methods: {

        /**
         * get_detail
         */
        get_detail: function () {
            var that = this;

            var formData = {};
            formData.token = that.token;
            formData.id = that.order_id;

            that.axios.post("/Orderreturn/order_return_detail", formData, {
                emulateJSON: true
            }).then(res => {
                var data = res;

                if (data.audit_time) {
                    that.order_status = 2;
                }
                if (data.user_return_time) {
                    that.order_status = 3;
                }
                if (data.delivery_time) {
                    that.order_status = 4;
                }
                if (data.last_time) {
                    that.order_status = 5;
                }

                that.order_data = data;
            });
        },

        /**
         * 确认收货
         */
        confirm_receipt(id) {
            const that = this;

            this.$confirm('是否确认收货?', '提示', {
                confirmButtonText: '是',
                cancelButtonText: '否',
                type: 'warning'
            }).then(() => {

                that.axios.post("/Orderreturn/return_goods", {
                    token: that.token,
                    id: that.order_id,
                }, {
                    emulateJSON: true
                }).then(res => {
                    that.$message.success('收货成功!');
                    that.get_detail();
                })
            }).catch();
        },

        /**
         * 拒绝退货
         */
        refuse_return() {
            const that = this;
            that.refuse_return_dia = true;
        },

        /**
         * 确认拒绝退货
         */
        con_refuse_return() {
            const that = this;

            var formData = {};
            formData.token = that.token;
            formData.id = that.order_id;
            formData.refuse = that.refuse_return_text;

            that.axios.post("/Orderreturn/no_return", formData, {
                emulateJSON: true
            }).then(res => {
                that.$message.success('操作成功!');
                that.get_detail();
                that.refuse_return_dia = false;
            });
        },

        /**
         * 同意退货
         */
        agreed_to_return() {
            const that = this;

            that.$confirm('确认同意退货吗?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {

                var formData = {};
                formData.token = that.token;
                formData.id = that.order_id;

                that.axios.post("/Orderreturn/yes_return", formData, {
                    emulateJSON: true
                }).then(res => {
                    that.$message.success('退货成功!');
                    that.get_detail();
                });

            }).catch(() => {});

        },

        /**
         * 备注
         */
        remarks() {
            const that = this;
            that.remarks_dia = true;
        },

        /**
         * 确认备注
         */
        confirm_remarks() {
            const that = this;

            var formData = {};
            formData.token = that.token;
            formData.id = that.order_id;
            formData.system_remark = that.order_data.system_remark;

            that.axios.post("/Orderreturn/system_remark", formData, {
                emulateJSON: true
            }).then(res => {
                that.$message.success('备注成功!');
                that.remarks_dia = 0;
                that.get_detail();
            });

        }

    }
}