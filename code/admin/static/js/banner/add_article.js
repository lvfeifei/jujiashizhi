import commJS from '../common.js';
import richText from "../../../src/page/common/richText";
export default {
    components: {
        richText
    },
    data() {
        return {
            // 表单
            form_data: {
                title: '',			// 标题
                picture: [],		// 图片
                sort: '',			// 排序
                content: "",		// 内容
                status: '1',		// z状态
                type: '1',			// 类型：2:大图；1:小图
            },

            rules: {
                picture: [{
                    required: true,
                    message: '请上传图片',
                    trigger: 'blur',
                    type: 'array',
                    min: 1,
                }],
                sort: [{
                    required: true,
                    message: '请填写排序',
                    trigger: 'blur'
                }],
                title: [{
                    required: true,
                    message: '请填写标题',
                    trigger: 'blur'
                }],
            },

            // 七牛云信息
            upload_img_url: this.adminApi.upload_url,
            uploadToken: {
                token: this.cookie.get('token')
            },
            postData: { folder: 'article' },
            domain: this.adminApi.img_url,
            
            article_id: ''
        }
    },

    /**
     * 进入页面加载
     */
    mounted: function () {
        var that = this;


        console.log("that.token");
        console.log(that.token);
        var query = that.$route.query;
        if (query.article_id) {
            that.article_id = query.article_id;
            that.getDetail();
        }
    },

    //方法
    methods: {
        /**
         * 获取详情
         */
        getDetail() {
            var that = this;

            that.axios.post("/help/help_show_edit", {
                token: that.token,
                help_id: that.article_id
            }, {
                emulateJSON: true
            }).then(res => {
                var data = res.info;
                if (data) {
                    that.form_data.title = data.title;
                    that.form_data.picture = [{
                        url: data.image
                    }];
                    that.form_data.sort = data.sort;
                    that.form_data.type = data.type;
                    that.form_data.content = data.content;
                    that.form_data.status = data.status.toString();

                }
            }).catch(err => {
                that.$message.error(err);
            })
        },

        /**
         * 保存预览
         */
        save() {
            const that = this;
            that.$refs.form_data.validate((valid) => {
                if (!valid) return that.$message.warning('请完整填写内容!');
                var formData = {};
                formData.token = that.token;
                formData.title = that.form_data.title;
                formData.bigimage = that.form_data.picture[0].url;
                formData.image = that.form_data.picture[0].url;
                formData.sort = that.form_data.sort;
                formData.type = that.form_data.type;
                formData.content = that.form_data.content;
                formData.status = that.form_data.status;
                var url = '/help/helpadd';
                if (that.article_id) {
                    formData.help_id = that.article_id;
                    url = '/help/helpedit';
                }
                that.axios.post(url, formData, {
                    emulateJSON: true
                }).then(() => {
                    that.$message.success(that.article_id ? '修改成功' : '添加成功');
                    that.$router.go(-1)
                }).catch(err => {
                    that.$message.error(err);
                })
            })
        },

        /**
         * 图片超限制
         */
        descExceed: function (t, e) {
            this.$message.warning("只能上传一张图片哦!")
        },

        /**
         * 图片上传成功
         */
        img_succ(res) {
            const that = this;
            console.log(res, 123);
            that.form_data.picture.push({
                url: that.domain + res.data.imgurl,
            })
        },

        /**
         *图片移除
         */
        del_img(file, fileList) {
            this.form_data.picture = fileList
        },

        /**
         * 返回
         */
        back() {
            this.$router.go(-1);
        },
        // 富文本内容输入
        editor_change(e) {
            this.form_data.content = e;
        },
    }
}
