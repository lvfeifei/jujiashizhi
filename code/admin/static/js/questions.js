export default {
    data() {
        return {
            tableData:[],
            count:0,
            page:1,
            limit:10,
        };
    },
    //进入页面加载
    mounted: function () {
        var that = this;

        that.getQuestionsList();

    },

    methods: {
        //查询列表
        getQuestionsList: function () {
            var that = this;
            //初始化数据
            that.tableData = [];

            //请求的参数
            var formData = {};
            formData.token = that.token;

            //请求登陆接口
            that.axios.post("/Quest/index", formData, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    if (res.list) {
                        for (var i in res.list) {
                            that.tableData.push({
                                i: i,
                                id: res.list[i].id,
                                title: res.list[i].question,
                                answer: res.list[i].answer,
                                sort: res.list[i].sort,
                                state: res.list[i].status,
                            });
                        }
                        that.count = res.count;
                    }
                }, function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //下一页
        handleCurrentChange:function (currentPage) {
            var that = this;
            that.page = currentPage;
        },
        //删除
        inquiry:function (e) {
            var that = this;
            that.$confirm('此操作将永久删除该问题, 是否继续?', '提示', {
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

        //执行删除
        delete_class:function (e) {
            var that = this;
            //请求登陆接口
            that.axios.post("/Quest/del", {
                token: that.token,
                id:e
            }, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${ '删除成功' }`
                    });
                    that.getQuestionsList();
                }, function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //修改
        edit:function (e) {
            var that = this;
            that.$router.push({path:'/setting/add_questions',query:{question_id:e}});
        },
    }
}