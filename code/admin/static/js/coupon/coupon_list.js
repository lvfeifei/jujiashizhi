export default {
    data() {
        return {
            tableData: [],
            count: 0,
            page: 1,
            limit: 10,
            keys: '',
            range:'',
            range_list:[{
                id:1,
                name:'全场优惠券'
            },{
                id:2,
                name:'指定商品优惠券'
            }],
            type:'',
            type_list:[{
                id:1,
                name:'直减券'
            },{
                id:2,
                name:'满减券'
            }],
            status:'',
            status_list:[{
                id:1,
                name:'推广中'
            },{
                id:2,
                name:'已过期'
            },{
                id:3,
                name:'已作废' 
            }],
        }
    },
    //进入页面加载
    mounted: function() {
        var that = this;
        
        that.getList();
    },

    //方法
    methods: {

        /**
         * 添加优惠券
         */
        add_coupon() {
            var that = this;
            that.$router.push('/coupon/add_coupon')
        },

        /**
         * 修改优惠券
         */
        edit_coupon(e) {
            var that = this;
            that.$router.push({
                path:'/coupon/add_coupon',
                query:{
                    coupon_id:e
                }
            })
        },

        /**
         * 已领取优惠券列表
         */
        to_list() {
            var that = this;
            that.$router.push('/coupon/receive_coupon')
        },

        /**
         * 获取列表
         */
        getList() {
            let that = this;
            let formData = {};

            formData.page = that.page;
            formData.token = that.token;
            formData.limit = that.limit;
            if (that.keys) {
                formData.key = that.keys;
            }
            if (that.range) {
                formData.range = that.range;
            }
            if (that.type) {
                formData.type = that.type;
            }
            if (that.status) {
                formData.status = that.status;
            }

            that.axios.post("/Coupon/coupon_list", formData, {
                emulateJSON: true
            }).then(res => {
                var data = res;
                if (data) {
                    data.list.forEach(ele => {
                        ele.type_text = ele.type==1?'直减券':'满减券';
                        ele.range_text = ele.range == 1 ?'全场优惠券':'指定商品优惠券';
                        ele.date = ele.date_type == 1 ? (ele.start_time+'--'+ele.end_time):('领取后'+ ele.day +'天过期')           
                    })
                    that.tableData = data.list;
                    that.count = data.count;
                }
            });
        },

        /**
         * 搜索
         */
        search() {
            const that = this;
            that.page = 1;
            that.getList();
        },

        /**
         * 查看详情
         */
        to_detail(e) {
            var that = this;
            that.$router.push({
                path: '/coupon/coupon_detail',
                query: {
                    coupon_id: e,
                }
            })
        },

        /**
         * 删除
         */
        del_coupon: function(e) {
            var that = this;

            that.$confirm('确定作废此优惠券吗?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                that.axios.post("/coupon/del_coupon", {
                    token: that.token,
                    coupon_id: e
                }, {
                    emulateJSON: true
                }).then(
                    function(res) {
                        that.$message.success('操作成功');
                        that.getList();
                    })
            }).catch(res => {});
        },

        /**
         * 下一页
         */
        handleCurrentChange: function(currentPage) {
            var that = this;
            that.page = currentPage;
            that.getList();
        },

    }
}