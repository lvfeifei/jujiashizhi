import commJS from '../common.js';
export default {
    data() {
        return {
            detail_data: {},
            tableData: [],
            count: 0,
            page: 1,
            limit: 10,
            radio:'1',
            invitation_code_width:160
        }
    },

    //进入页面加载
    mounted: function () {
        var that = this; 
        that.detail();  
    },

    methods: {

        // 下载二维码
        down_invitation_code(){
            // console.log(this.dialog_visible_item.invitation_code) 
            // // this.downloadByBlob(this.dialog_visible_item.invitation_code,'66.png')
            // var a = document.createElement("a"); //创建一个<a></a>标签
            // a.href = this.dialog_visible_item.invitation_code; // 给a标签的href属性值加上地址，注意，这里是绝对路径，不用加 点.
            // a.download = "xxx.png"; //设置下载文件文件名，这里加上.xlsx指定文件类型，pdf文件就指定.fpd即可
            // a.style.display = "none"; // 障眼法藏起来a标签
            // document.body.appendChild(a); // 将a标签追加到文档对象中
            // a.click(); // 模拟点击了a标签，会触发a标签的href的读取，浏览器就会自动下载了
            // a.remove(); // 一次性的，用完就删除a标签
           let url = this.adminApi.api_url + '/bead_house/download_code?code_url=' + this.detail_data.invitation_code ;
           window.location.href = url
        },

        /**
       * 下一页
       */
        // handleCurrentChange(currentPage) {
        //     var that = this;
        //     that.page = currentPage;
        //     that.getList();
        // },

        /**
       * 获取列表
       */
        // getList() {
        //     let that = this;
        //     let formData = {};
        //     formData.page = that.page;
        //     formData.limit = that.limit;
        //     formData.id = that.order_id;
        //     that.axios.post("/user/patient_list", formData, {
        //         emulateJSON: true,
        //     }).then(
        //         function (res) {
        //             let data = res.data
        //             if (data) {
        //                 // console.log(data.list)
        //                 that.tableData = data.list;
        //                 that.count = data.count;
        //                 that.page = that.page;
        //             }
        //         }).catch(err => { that.$message.error(err); });
        // },

        //查询详情
        detail: function () {
            var that = this;
            //请求登陆接口
            that.axios.post("/bead_house/bead_house_info", {
                token: that.token, 
            }, {
                emulateJSON: true
            }).then(
                function (res) {
                    that.detail_data = res.data;
                },
                function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'处理异常'}`
                    });
                });
        },

        /**
       * 修改
       */
        edit: function (row) {
            let that = this;
            commJS.save_page(that)
            that.$router.push({
                path: '/index/pingce_jilu_detail',
                query: {
                    order_id: row.id,
                    user_id: row.user_id
                }
            });
        },


        /**
     * 返回
     */
        back() {
            this.$router.go(-1);
        },
    }
}
