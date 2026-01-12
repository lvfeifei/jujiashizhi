export default {
    data() {
        return {
            class_name:'',
            class_sort:'',
            class_id:0
        };
    },
    //进入页面加载
    mounted: function () {
        var that = this;

        if(that.$route.query.class_id) {
            that.class_id = that.$route.query.class_id;
            that.detail();
        }
    },

    methods: {
        //发送请求
        addClass: function () {
            var that = this;
            if(!that.class_name){
                that.$message({
                    type: 'error',
                    message: `操作提示: ${ '请输入分类名称' }`
                });
                return false;
            }
            //请求登陆接口
            that.axios.post("/classif/add", {
                token: that.token,
                name:that.class_name,
                sort:that.class_sort
            }, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${ '添加成功' }`
                    });
                    that.$router.push('/nav1/shop_grouping');
                }, function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //查询单个分类值
        detail:function () {
            var that = this;
            //请求登陆接口
            that.axios.post("/classif/show_edit", {
                token: that.token,
                id:that.class_id,
            }, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    that.class_name = res.name;
                    that.class_sort = res.sort;
                }, function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //返回
        cancel:function () {
            var that = this;
            that.$router.push('/nav1/shop_grouping');
        },

        //修改
        editClass:function () {
            var that = this;
            //请求登陆接口
            that.axios.post("/classif/edit", {
                token: that.token,
                id:that.class_id,
                name:that.class_name,
                sort:that.class_sort
            }, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${ '修改成功' }`
                    });
                    that.$router.push('/nav1/shop_grouping');
                }, function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        }
    }
}