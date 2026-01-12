export default {
    data() {
        return {
            domain:'',//图片域名
            postData: {},
            fileList:[],
            path_url:'',
            src:''
        }
    },
    //进入页面加载
    mounted: function () {
        var that = this;
        that.getQiNiuToken();
        that.src = '/Experience/excel_tel';
    },

    methods: {
        //获取七牛云token
        getQiNiuToken: function() {
            var that = this;
            //请求
            that.axios.post("/Qiniu/getToken", {
                token: that.token,
            }, {
                emulateJSON: true
            }).then(
                function(res) {
                    // 处理成功的结果
                    that.postData = {
                        token: res.upToken,
                    }
                    that.domain=res.domain;
                },
                function() {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${ '处理异常' }`
                    });
                });
        },

        //文件超出个数限制时的钩子
        handleExceed(files, fileList) {
            this.$message.warning(`当前限制选择 1 个文件，本次选择了 ${files.length} 个文件，共选择了 ${files.length + fileList.length} 个文件`);
        },
        //删除文件之前的钩子，参数为上传的文件和文件列表，若返回 false 或者返回 Promise 且被 reject，则停止上传。
        beforeRemove(file, fileList) {
            return this.$confirm(`确定移除 ${ file.name }？`);
        },

        //校验
        beforeAvatarUpload(file) {
            var testmsg=file.name.substring(file.name.lastIndexOf('.')+1)
            const extension = testmsg === 'xls'
            // const extension2 = testmsg === 'xlsx'
            const isLt2M = file.size / 1024 / 1024 < 1
            if(!extension) {
                this.$message({
                    message: '上传文件只能是 xls格式!',
                    type: 'warning'
                });
            }
            if(!isLt2M) {
                this.$message({
                    message: '上传文件大小不能超过 1MB!',
                    type: 'warning'
                });
            }
            return extension  && isLt2M
        },

        //上传成功
        handleAvatarSuccess(res){
            var that = this;
            that.path_url = that.domain + res.key;
        },
        //上传失败
        handleError(res){
        },


        //确定批量发货
        confirmSend:function () {
            var that = this;
            if(!that.path_url){
                that.$confirm('请上传正确格式的文件?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {

                }).catch(() => {

                });
                return false;
            }
            that.$confirm('此操作将执行批量发货, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                that.axios.post("/Experience/upload_file", {
                    url: that.path_url,
                }, {
                    emulateJSON: true
                }).then(
                    function(res) {
                        // 处理成功的结果
                        that.$message({
                            type: 'success',
                            message: `操作提示: ${ '操作成功' }`
                        });
                    },
                    function() {
                        // 处理失败的结果
                        that.$message({
                            type: 'error',
                            message: `操作提示: ${ '处理异常' }`
                        });
                    });
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消删除'
                });
            });
        },

    }
}