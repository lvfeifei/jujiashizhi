// import comm from '../common.js';

export default {
    data() {
        return {
            select_name: '1',
            input_content: '',
            page: 1,
            limit: 10,
            count: '',
            type: '0',
            date: [],
            order_list: [],
            refund_dia: false,
            delivergoods_dia: false,
            logisticsList: [],
            ord_type: '1',
            refund_pic: ''

        }
    },
    //进入页面加载
    mounted: function () {
        var that = this;

        that.getOrderList();
    },

    methods: {

        /**
         * 请订单列表api
         */
        getOrderList: function () {
            var that = this;

            var formData = {};
            formData.token = that.token;
            formData.page = that.page;
            formData.limit = that.limit;
            formData.type = that.ord_type;
            formData.select_name = that.select_name;
            formData.status = that.type;
            formData.input_content = that.input_content;

            if (that.date.length > 0) {
                formData.start_time = comm.formatTime(that.date[0]);
                formData.end_time = comm.formatTime(that.date[1]);
            }

            //请求登陆接口
            that.axios.post("/Orderreturn/index", formData, {
                emulateJSON: true
            }).then(res => {
                var data = res;

                that.order_list = data.list;
                that.count = data.count;
            });
        },

        /**
         * 下一页
         */
        handleCurrentChange: function (pageNum) {
            var that = this;
            that.page = pageNum;
            that.getOrderList();
        },

        /**
         * 搜索
         */
        search: function () {
            var that = this;
            that.getOrderList();
        },

        /**
         * to_detail
         */
        to_detail(e) {
            const that = this;
            that.$router.push({
                path: '/nav3/order_returned_detail',
                query: {
                    order_id: e
                }
            })
        },

        /**
         * 选择类型
         */
        handleClick(tab) {
            let that = this;
            that.ord_type = tab;
            that.page = 1;
            that.getOrderList();
        },

        /**
         * 同意退货
         */
        agreed_return() {
            const that = this;

            this.$confirm('确认退货吗?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {

                that.$message.success('退货成功!');
            }).catch();
        },

        /**
         * 备注
         */
        remarks() {

        },

        /**
         * 退款
         */
        to_refund(e) {
            const that = this;
            that.return_pic_ind = e;
            that.totle_pic = that.order_list[e].total_price;
            that.refund_dia = true;
        },

        /**
         * 确认退款
         */
        confirm_refund() {
            const that = this;

            if (that.refund_pic > that.totle_pic) {
                that.$message.warning('请正确输入金额!');
                return;
            }; 

            this.$confirm('确认退款吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {

                var id = that.order_list[that.return_pic_ind].id;
                var order_goods_id = that.order_list[that.return_pic_ind].order_goods_id;

                that.axios.post("/wechat/order_refund", {
                    token: that.token,
                    system_manager_id: that.user_id,
                    id: id,
                    return_price: that.refund_pic,
                    order_goods_id:order_goods_id
                }, {
                    emulateJSON: true
                }).then(res => {

                    that.refund_dia = false;
                    that.$message.success('退款成功!');
                    that.getOrderList();
                });

            }).catch(() => {});
        },

        /**
         * 确认收货
         */
        confirm_order(id) {
            const that = this;

            this.$confirm('是否确认收货?', '提示', {
                confirmButtonText: '是',
                cancelButtonText: '否',
                type: 'warning'
            }).then(() => {

                that.axios.post("/Orderreturn/return_goods", {
                    token: that.token,
                    id: id,
                }, {
                    emulateJSON: true
                }).then(res => {
                    that.$message.success('收货成功!');
                    that.getOrderList();
                })
            }).catch();
        }

    }
}