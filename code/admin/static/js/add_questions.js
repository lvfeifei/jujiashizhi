export default {
    data() {
        return {
            question_name:'',
            question_sort:'',
            answer:'',
            question_id:0
        };
    },
    //进入页面加载
    mounted: function () {
        var that = this;

        if(that.$route.query.question_id) {
            that.question_id = that.$route.query.question_id;
            that.detail();
        }
    },

    methods: {
        //发送请求
        addQuestions: function () {
            var that = this;
            if(!that.question_name){
                that.$message({
                    type: 'error',
                    message: `操作提示: ${ '请输入问题' }`
                });
                return false;
            }
            if(!that.answer){
                that.$message({
                    type: 'error',
                    message: `操作提示: ${ '请输入答案' }`
                });
                return false;
            }
            //请求登陆接口
            that.axios.post("/Quest/add", {
                token: that.token,
                question:that.question_name,
                answer:that.answer,
                sort:that.question_sort,
            }, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${ '添加成功' }`
                    });
                    that.$router.push('/setting/questions');
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
            that.axios.post("/Quest/show_edit", {
                token: that.token,
                id:that.question_id,
            }, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    that.question_name = res.question;
                    that.answer = res.answer;
                    that.question_sort = res.sort;
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
            that.$router.push('/setting/questions');
        },

        //修改
        editQuestion:function () {
            var that = this;
            //请求登陆接口
            that.axios.post("/Quest/edit", {
                token: that.token,
                id:that.question_id,
                question:that.question_name,
                answer:that.answer,
                sort:that.question_sort,
            }, {emulateJSON: true}).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${ '修改成功' }`
                    });
                    that.$router.push('/setting/questions');
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