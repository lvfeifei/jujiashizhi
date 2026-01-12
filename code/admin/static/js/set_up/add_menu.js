import commJS from '../common.js';
export default {
    data() {
        return {
            menu_id: '',
            ruleForm: {
                higher_level: '',
                menu_name: '',
                url: '',
                img: [],
                sort: '',
                status: '1'
            },
            rules: {
                menu_name: [{
                    required: true,
                    message: '请输入菜单名称',
                    trigger: 'blur'
                }],
                sort: [{
                    required: true,
                    message: '请输入菜单排序',
                    trigger: 'blur'
                }]
            },

            // 上传图片时携带的参数
            upload_img_url: this.adminApi.upload_url,
            uploadToken: {
                token: this.cookie.get('token')
            },
            postData: { folder: 'system_menu' },

            descImageUrl: '', //图片
            desc_img: false, //是否展示删除

            // 下拉框
            options: [],
        }
    },
    //进入页面加载
    mounted: function () {
        var that = this;

        if (that.$route.query.id) {
            that.menu_id = that.$route.query.id;
            that.getDetail();
        }

        that.getOptionData();
    },

    //方法
    methods: {
        /**
         * 获取列表
         */
        getOptionData() {
            let that = this;

            //请求的数据
            let formData = {};
            formData.token = that.token;

            that.axios.post("/system_menu/one_menu", formData, {
                emulateJSON: true
            }).then(
                function (res) {
                    var data = res.data;
                    if (data) {
                        that.options = data;
                    }
                });
        },

        /**
         * 获取详情
         */
        getDetail(e) {
            let that = this;

            that.axios.post("/System/info", {
                token: that.token,
                id: that.menu_id
            }, {
                emulateJSON: true
            }).then(
                function (res) {
                    var data = res.data;
                    if (data) {
                        that.ruleForm.higher_level = data.pid;
                        that.ruleForm.menu_name = data.name;
                        that.ruleForm.url = data.url;
                        if (data.icon) {
                            that.ruleForm.img = [{
                                name: 'img',
                                url: data.icon
                            }];
                        }

                        that.ruleForm.sort = data.sort;
                        that.ruleForm.status = data.status.toString();
                    }
                });
        },

        /**
         * 保存
         */
        save() {
            let that = this;

            let formData = {};
            formData.token = that.token;
            formData.pid = that.ruleForm.higher_level;
            formData.name = that.ruleForm.menu_name;
            formData.icon = !that.ruleForm.higher_level ? that.ruleForm.img[0].url : '';
            formData.sort = that.ruleForm.sort;
            formData.url = that.ruleForm.url;
            formData.status = that.ruleForm.status;

            if (that.menu_id) {
                formData.id = that.menu_id;
            }

            that.axios.post((that.menu_id ? "/system/edit" : "/system/add"), formData, {
                emulateJSON: true
            }).then(
                function (res) {
                    var data = res;
                    if (data.status == 1) {
                        that.$message.success(!that.menu_id ? '添加成功' : '修改成功');
                        that.$router.push('/set_up/menu_list');
                    }
                });
        },

        //上传图片
        /**
         * 删除
         * @param file
         * @param fileList
         */
        descRemove(file, fileList) {
            var that = this;
            that.imgList = fileList;
        },

        /**
         * 上传前
         */
        beforeupload(file) {
        },

        /**
         * 上传时
         */
        on_progress(event, file, fileList) {
        },

        // 预览时
        descPictureCardPreview(file) {
            this.descImageUrl = file.url;
            this.desc_img = true;
        },
        /**
         * 显示错误
         * @param res
         */
        descError(res) {
        },

        /**
         * 上传成功后在图片框显示图片
         */
        descAvatarSuccess(res, file) {
            const that = this;
            that.ruleForm.img = [{
                name: 'img',
                url:  res.data.imgurl,
            }];

        },

        /**
         * 文件超出个数限制时的钩子
         * @param files
         * @param fileList
         */
        descExceed(files, fileList) {
            this.$message.warning('只能上传一张图片哦!');
        },


    }
}
