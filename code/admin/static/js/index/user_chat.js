export default {
    data() {
        return {
            tableData: [],
            count: 0,
            page: 1,
            limit: 10,
            key_words: '',
            user_type: '',
            is_loading: false,
            data_time: ''
        }
    },
    //进入页面加载
    mounted: function () { 
        var that = this;
        that.token = sessionStorage.getItem('access-token');
        that.getList();
    },

    //方法
    methods: {
        /**
         * 保存页数
         */
        save_page() {
            var that = this;
            // 记录当前页
            sessionStorage.setItem('curr_page', that.page)
        },

        /**
         * 获取列表
         */
        getList() {
            let that = this;

            let formData = {};
            formData.token = that.token;

            var curr_page = sessionStorage.getItem('curr_page');
            that.page = curr_page ? curr_page - 0 : that.page;

            // if (that.data_time != null) {
            //     if (that.data_time.length > 0) {
            //         formData.start_time = that.formatDateTime(that.data_time[0]);
            //         formData.end_time = that.formatDateTime(that.data_time[1]);
            //     }
            // }
            formData.page = that.page;
            formData.limit = that.limit;

            // if (that.key_words) {
            //     formData.key = that.key_words;
            // }

            that.axios.post("/User_chat/index", formData, {
                emulateJSON: true
            }).then(
                function (data) {  
                    if (data) { 
                        that.tableData = data.list;
                        that.count = data.count;
                        that.page = curr_page ? curr_page : that.page;
                        curr_page ? sessionStorage.removeItem('curr_page') : '';
                    }
                });
        },

        /**
         * 筛选
         */
        search: function () {
            let that = this;
            that.page = 1;
            that.getList();
        },

        /**
         * 查看详情
         */
        to_detail(user_id) {
            var that = this;
            that.save_page();
            this.$router.push({
                path: '/index/user_chat_detail',
                query: {
                    user_id: user_id
                }
            })
        },

        /**
         * 时间转换
         * @param date
         * @returns {string}
         */
        formatDateTime: function (date) {
            var y = date.getFullYear();
            var m = date.getMonth() + 1;
            m = m < 10 ? ('0' + m) : m;
            var d = date.getDate();
            d = d < 10 ? ('0' + d) : d;
            var h = date.getHours();
            var minute = date.getMinutes();
            minute = minute < 10 ? ('0' + minute) : minute;
            return y + '-' + m + '-' + d;
        },


        /**
         * 下一页
         */
        handleCurrentChange: function (currentPage) {
            var that = this;
            that.page = currentPage;
            that.getList();
        },
    }
}
