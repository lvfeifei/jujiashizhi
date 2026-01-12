export default {
    data() {
        return {
            tableData: [],
            count: 0,
            page: 1,
            limit: 10,
            keys: '',
            user_type: '',
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
            if (that.user_type) {
                formData.user_type = that.user_type;
            }

            that.axios.post("/user/distribution_user", formData, {
                emulateJSON: true
            }).then(res => {
                var data = res;
                if (data) {
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
                    user_id: e,
                }
            })
        },

        /**
         * 删除
         */
        join_black: function(e, status) {
            var that = this;

            that.$confirm('确定将此用户' + (status == 1 ? '加入' : '移除') + '黑名单吗?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                that.axios.post("/user/set_status", {
                    token: that.token,
                    id: e
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