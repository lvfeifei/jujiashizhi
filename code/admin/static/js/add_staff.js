export default {
    data() {
        return {
            role_id:0,
            mobile:'',
            name:'',
            username:'',
            identity:"1",
            userPassWord:''
        };
    },
    //进入页面加载
    mounted: function () {
        var that = this;

        if(that.$route.query.role_id) {
            that.role_id = that.$route.query.role_id;
            that.detail();
        }
    },

    methods: {
        //发送请求
        addClass: function () {
            var that = this;
            if(!that.mobile){
                that.$message({
                    type: 'error',
                    message: `操作提示: ${ '请输入员工手机号' }`
                });
                return false;
            }
            if(!that.name){
                that.$message({
                    type: 'error',
                    message: `操作提示: ${ '请输入员工姓名' }`
                });
                return false;
            }
            if(!that.username){
                that.$message({
                    type: 'error',
                    message: `操作提示: ${ '请输入账号' }`
                });
                return false;
            }
            //请求登陆接口
            that.axios.post("/Manager/add", {
                token: that.token,
                mobile:that.mobile,
                truename:that.name,
                username:that.username,
                password:that.userPassWord,
                identity:that.identity
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

        //查询单个
        detail:function () {
            var that = this;
            //请求登陆接口
            that.axios.post("/Manager/managerDetail", {
                token: that.token,
                id:that.role_id,
            }, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    that.mobile = res.mobile;
                    that.name = res.truename;
                    that.username = res.username;
                    that.identity=res.identity.toString();
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
            that.axios.post("/Manager/edit", {
                token: that.token,
                id:that.role_id,
                mobile:that.mobile,
                truename:that.name,
                username:that.username,
                identity:that.identity,
                newPassword:that.userPassWord
            }, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${ '添加成功' }`
                    });
                    that.$router.push('/setting/staff');
                }, function (res) {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ res.msg }`
                    });
                });
        }
    }
}