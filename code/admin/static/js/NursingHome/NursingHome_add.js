import richText from "../../../src/page/common/richText_1";
import commJS from '../common.js';
export default {
    components: {
        richText,
    },
    data() {
        return {
            squareUrl: "https://cube.elemecdn.com/9/c2/f0ee8a3c7c9638a54940382568c9dpng.png",
            // 表单
            form_data: {
                title: '',   //  养老院名称
                picture: [],
                address: '',  //  地址
                name: '',     //  联系人
                mobile: '',   //  联系电话
                username: '', //  账号
                password: '', //  密码
                status: 1,
            },
            rules: {
                picture: [{
                    required: true,
                    message: '请上传图片',
                    trigger: 'blur',
                    type: 'array',
                    min: 1,
                }],
                title: [{
                    required: true,
                    message: '请填写养老院名称',
                    trigger: 'blur'
                }],
                address: [{
                    required: true,
                    message: '请填写养老院地址',
                    trigger: 'blur'
                }],
                name: [{
                    required: true,
                    message: '请填写联系人姓名',
                    trigger: 'blur'
                }],
                mobile: [{
                    required: true,
                    message: '请填写联系人电话',
                    trigger: 'blur'
                }],
                username: [{
                    required: true,
                    message: '请填写登陆账号',
                    trigger: 'blur'
                }],
            },
            // 七牛云信息
            upload_img_url: this.adminApi.upload_url,
            upload_video_url: this.adminApi.upload_video_url,
            img_url: this.adminApi.img_url,

            upload_header: {
                token: this.cookie.get('token')
            },
            postData: {
                folder: "zixun"
            },
            domain: '',
            id: 0
        }
    },

    /**
     * 进入页面加载
     */
    async mounted() {
        var that = this;
        var query = that.$route.query;
        if (query.id) {
            that.id = query.id;
            that.getDetail();
        }

    },

    //方法
    methods: {
        /**
         * 富文本改变时
         */
        editor_change(e) {
            this.form_data.content = e;
        },

        /**
         * 获取详情
         */
        getDetail() {
            var that = this;
            var formData = {}
            formData.id = that.id
            that.axios.post("/bead_house/show", formData, {
                emulateJSON: true
            }).then(
                function (res) {
                    let { data } = res
                    if (data) {
                        that.form_data = data
                        that.form_data.picture = [{
                            url: data.logo
                        }];
                    }
                }).catch(err => { });
        },

        /**
         * 保存预览
         */
        save() {

            const that = this;
            that.$refs.form_data.validate((valid) => {
                if (!valid) return that.$message.warning('请完整填写内容!');
                if (that.form_data.picture.length == 0) {
                    return that.$message.warning('请上传小图!')
                }
                that.form_data.logo = that.form_data.picture[0].url;

                if(that.form_data.password){
                  // 正则验证
                  let reg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[^]{0,20}$/;
                  if(!reg.test(that.form_data.password)){
                    return that.$message({
                      type: 'error',
                      message: '密码需要同时包含英文大小写和数字'
                    });
                  }
              }

                var url = '/bead_house/add';
                if (that.id) {
                    url = '/bead_house/edit';
                    that.form_data.id = that.id;
                }
                that.axios.post(url,that.form_data, {
                    emulateJSON: true
                }).then(
                    function (data) {
                        if(data.status) {
                            that.$message.success(that.id ? '修改成功' : '添加成功');
                            that.$router.go(-1)
                        }else{
                            that.$message.error(data.msg);
                        }

                    }).catch(err => { });
            })
        },

        /**
         * 图片超限制
         */
        descExceed: function (t, e) {
            this.$message.warning("只能上传一张图片哦!")
        },

        /**
         * 图片超限制
         */
        descExceed_bigimage: function (t, e) {
            this.$message.warning("只能上传一张图片哦!")
        },

        // 视频限制
        video_descExceed: function (t, e) {
            this.$message.warning("只能上传一个视频!")
        },

        /**
         * 视频上传成功
         */
        h5_img_succ(file) {
            const that = this;
            that.form_data.video_arr = [{
                name: 'img',
                url: file.data.imgurl
            }];
            that.form_data.video = file.data.imgurl
        },

        /**
         * 图片上传成功
         */
        img_succ(file, fileList) {
            const that = this;
            // console.log(file);
            that.form_data.picture = [{
                name: 'img',
                url: file.data.imgurl
            }];
        },

        /**
         * 图片上传成功
         */
        img_succ_bigimage(file, fileList) {
            const that = this;
            // console.log(file);
            that.form_data.bigimage = [{
                name: 'img',
                // url: that.domain + '/' +  file.key
                url: file.data.imgurl
            }];
        },


        /**
         *图片移除
         */
        del_img(file, fileList) {
            this.form_data.picture = fileList
        },

        /**
         *h5图片移除
         */
        del_h5_img(file, fileList) {
            let that = this
            that.form_data.video_arr = []
            that.form_data.video = ''
        },

        /**
         *图片移除
         */
        del_bigimage(file, fileList) {
            this.form_data.bigimage = fileList
        },

        /**
         * 返回
         */
        back() {
            this.$router.go(-1);
        }
    }
}
