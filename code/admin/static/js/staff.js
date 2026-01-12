export default {
    data() {
        return {
            tableData: [],
            count: 0,
            page: 1,
            limit: 10,
            user_id:0
        }
    },
    //进入页面加载
    mounted: function() {
        var that = this;

        //请求员工列表
        that.getRebateSet();
    },

    methods: {
        //请求员工列表api
        getRebateSet: function() {
            var that = this;
            //初始化数据
            that.tableData = [];

            //请求登陆接口
            that.axios.post("/Manager/index", {
                token: that.token,
                id:that.user_id
            }, {
                emulateJSON: true
            }).then(
                function(res) {
                    // 处理成功的结果
                    if(res.list) {
                        for(var i in res.list) {
                            that.tableData.push({
                                i: i,
                                id: res.list[i].id,
                                account: res.list[i].username,
                                name: res.list[i].truename,
                                mobile: res.list[i].mobile,
                                add_time: res.list[i].register_time,
                                jurisdiction: res.list[i].role_name,
                            });
                        }
                        that.count = res.count;
                    }
                },
                function() {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //下一页
        handleCurrentChange: function(currentPage) {
            var that = this;
            that.page = currentPage;
        },

        //编辑
        edit: function(e) {
            var that = this;
            that.$router.push({
                path: '/setting/add_staff',
                query: {
                    role_id: e
                }
            });
        },

        //删除
        inquiry: function(e) {
            var that = this;
            that.$confirm('此操作将永久删除该账号, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                that.delete_class(e);
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消删除'
                });
            });
        },

        delete_class: function(e) {
            var that = this;
            //请求登陆接口
            that.axios.post("/Manager/del", {
                token: that.token,
                id: e
            }, {
                emulateJSON: true
            }).then(
                function(res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${ '删除成功' }`
                    });
                    that.getRebateSet();
                },
                function() {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        }

    }
}