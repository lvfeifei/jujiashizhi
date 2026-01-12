export default {
    data() {
        return {
            tableData: [],
            page: 1,
            limit: 10,
            user_mobile: '',
            nickname: '',
            count: 0,
            dialogVisible: false, //备注弹窗
            nickRemark:'',
            invitation_id:0,

        }
    },

    //进入页面加载
    mounted: function() {
        var that = this;

        //获取邀请者
        that.getRebateList();
    },

    methods: {
        //请求api
        getRebateList: function() {
            var that = this;
            //初始化数据
            that.tableData = [];

            //请求的数据
            var formData = {};
            formData.token = that.token;

            if(that.user_mobile) {
                formData.mobile = that.user_mobile;
            }
            if(that.nickname) {
                formData.nickname = that.nickname;
            }
            //请求邀请者列表
            that.axios.post("/user_detail/user_present_list", formData, {
                emulateJSON: true
            }).then(
                function(res) {
                    // 处理成功的结果
                    if(res.list) {
                        for(var i in res.list) {
                            that.tableData.push({
                                i: i,
                                id: res.list[i].id,
                                nickname:res.list[i].nickname,
                                picture: "<img src='" + res.list[i].avatar_url + "' style='width:80px;height:80px;border-radius:80px;padding: 20px'/>",
                                mobile:res.list[i].mobile,
                                real_name:res.list[i].real_name,
                                bank:res.list[i].bank,
                                bank_card:res.list[i].bank_card,
                                price:res.list[i].price,
                                status_info:res.list[i].status_info,
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
            that.getClassList();
        },

        //商品下架
        edit_status:function (e) {
            var that= this;
            that.$confirm((that.tableData[e].status ==1) ? '是否确认转账?':'是否确认转账?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                //请求api
                that.axios.post("/user_detail/update_present_status", {
                    token : that.token,
                    id:that.tableData[e].id,
                }, {emulateJSON: true}).then(
                    function (res) {
                        // 处理成功的结果
                        that.getRebateList();
                        that.$message({
                            type: 'success',
                            message: `操作提示: ${ '操作成功' }`
                        });
                    }, function () {
                        // 处理失败的结果
                        that.$message({
                            type: 'error',
                            message: `操作提示: ${ '处理异常' }`
                        });
                    });
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消'
                });
            });
        },
    }
}