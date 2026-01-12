<template>
    <div class="line_nor">
        <quill-editor v-model="describe" v-loading="loading2" element-loading-text="上传中..." :disabled='disabled'
                      element-loading-spinner="el-icon-loading" ref="myQuillEditor" :options='editorOption'
                      class="quill-editor" @ready="onEditorReady($event)"
                      @change="onEditorChange($event)"></quill-editor>

        <el-upload :headers="upload_header"  style="display:none;" multiple accept="image/*" :before-upload='before_upload'
                   :on-progress='img_progress' ref="upload_img" class="upload_img" :action="upload_img_url"
                   :show-file-list="false" :on-success="AvatarSuccess" :data="postData"></el-upload>

        <el-upload  :headers="upload_header"  style="display:none;" accept="video/*" :before-upload='before_upload' :on-progress='img_progress'
                   ref="upload_video" class="upload_video" :action="upload_img_url" :show-file-list="false"
                   :on-success="AvatarSuccess" :data="postData"></el-upload>
    </div>
</template>
<script>
    import {quillEditor} from "vue-quill-editor";
    import Quill from "quill";
    import commJS from "../../../static/js/common";

    var toolbarOptions = [
        ["bold", "italic", "underline", "strike"],
        // [{header: 1}, {header: 2}],
        [{list: "ordered"}, {list: "bullet"}],
        [{script: "sub"}, {script: "super"}],
        [{indent: "-1"}, {indent: "+1"}],
        [{direction: "rtl"}],
        // [{size: ["small", false, "large", "huge"]}],
        [{header: [1, 2, 3, 4, 5, 6, false]}],
        [{color: []}, {background: []}],
        [{align: []}],
        // ['clean'],
        ["image"]
    ];

    // 这里引入修改过的video模块并注册
    // import Video from '../../../static/js/video';
    // Quill.register(Video, true);

    export default {
        components: {
            quillEditor
        },
        name: "richText",
        data() {
            return {
                // 富文本编辑器配置
                editorOption: {
                    placeholder: "",
                    theme: "snow", //样式
                    modules: {
                        toolbar: {
                            container: toolbarOptions, //工具栏
                            handlers: {
                                image: function (value) {
                                    if (value) {
                                        document
                                            .querySelector(".upload_img input")
                                            .click();
                                    } else {
                                        this.quill.format("image", false);
                                    }
                                },
                                video: function (value) {
                                    if (value) {
                                        document
                                            .querySelector(".upload_video input")
                                            .click();
                                    } else {
                                        this.quill.format("video", false);
                                    }
                                }
                            }
                        }
                    }
                },
                
                loading2: false,
                // 七牛云地址
                upload_img_url: this.adminApi.upload_url, 
                upload_header:{
                    token:this.cookie.get('token')
                },
                postData: {
                    folder:"ceping"
                },
                //图片域名
                domain: "",
                upload_length: ""
            };
        },

        props: {
            describe: "",
            placeholder: "",
            // 禁用
            disabled: true
        },

        /**
         * 创建前
         */
        created() {
            var that = this;
            that.editorOption.placeholder = that.placeholder;
        },

        /**
         * 加载后
         */
        mounted: function () {
            var that = this; 
            // commJS.getQiNiuToken(that);
            console.log(that.describe,'======');
        },

        //方法
        methods: {
            // 富文本编辑器配置
            onEditorChange(e) {
                var that = this;
                that.$emit("editor_change", e.html);
            },

            /**
             *富文本信息
             */
            onEditorReady(e) {
            },

            //上传成功
            AvatarSuccess(res, file, list) {
                //上传成功后在图片框显示图片
                let that = this;
                if(that.postData.type.indexOf("video") != -1 ){
                    that.loading2 = false;
                    // 获得文件上传后的URL地址 
                    if (res) {
                        // 将文件上传后的URL地址插入到编辑器文本中
                        // let value = that.domain + res.data.imgurl;
                        let value =  res.data.imgurl;
                        // 获取光标位置对象，里面有两个属性，一个是index 还有 一个length，这里要用range.index，即当前光标之前的内容长度，然后再利用 insertEmbed(length, 'image', imageUrl)，插入图片即可。
                        let length = that.$refs.myQuillEditor.quill.getSelection()
                            .index;
                        that.addRange = that.$refs.myQuillEditor.quill.getSelection();
                        value = value.indexOf("http") !== -1 ? value : "http:" + value;

                        that.$refs.myQuillEditor.quill.insertEmbed(
                            that.addRange !== null ? that.addRange.index : 0,
                            // file.raw.type.split("/")[0] == "image" ? "image" : "video",
                            file.raw.type.split("/")[0],
                            value,
                            Quill.sources.USER
                        ); // 调用编辑器的 insertEmbed 方法，插入URL

                        // 调整光标到最后
                        that.$refs.myQuillEditor.quill.setSelection(length + 1);
                    } else {
                        that.$message.error(`${that.uploadType}插入失败`);
                    }
                    that.$refs["upload_img"].clearFiles(); // 插入成功后清除input的内容
                    that.$refs["upload_video"].clearFiles(); // 插入成功后清除input的内容
                }else {
                    let imgArr = [];
                    for (var j = 0; j < that.$refs.upload_img.uploadFiles.length; j++) {
                        if (that.$refs.upload_img.uploadFiles[j].status == "success") {
                            if (!that.$refs.upload_img.uploadFiles[j].filedId) {
                                imgArr.push(
                                    that.$refs.upload_img.uploadFiles[j].response
                                );
                            } else {
                                imgArr.push(
                                    that.$refs.upload_img.uploadFiles[j].filedId
                                );
                            }
                        }
                    }

                    if (imgArr.length == that.upload_length) {
                        imgArr.forEach(ele => {
                            that.loading2 = false;
                            // 获得文件上传后的URL地址
                            if (ele) {
                                // 将文件上传后的URL地址插入到编辑器文本中
                                // let value = that.domain + ele.key;
                                let value =   ele.data.imgurl; 
                                // 获取光标位置对象，里面有两个属性，一个是index 还有 一个length，这里要用range.index，即当前光标之前的内容长度，然后再利用 insertEmbed(length, 'image', imageUrl)，插入图片即可。
                                let length = that.$refs.myQuillEditor.quill.getSelection()
                                    .index;
                                that.addRange = that.$refs.myQuillEditor.quill.getSelection();
                                value =
                                    value.indexOf("http") !== -1
                                        ? value
                                        : "http:" + value;

                                that.$refs.myQuillEditor.quill.insertEmbed(
                                    that.addRange !== null ? that.addRange.index : 0,
                                    // file.raw.type.split("/")[0] == "image" ? "image" : "video",
                                    file.raw.type.split("/")[0],
                                    value,
                                    Quill.sources.USER
                                ); // 调用编辑器的 insertEmbed 方法，插入URL

                                // 调整光标到最后
                                that.$refs.myQuillEditor.quill.setSelection(length + 1);
                            } else {
                                that.$message.error(`${that.uploadType}插入失败`);
                            }
                            that.$refs["upload_img"].clearFiles(); // 插入成功后清除input的内容
                            that.$refs["upload_video"].clearFiles(); // 插入成功后清除input的内容
                        });
                    }
                }
            },

            /**
             * 上传时
             */
            img_progress(res, file, list) {
                let that = this;

                that.upload_length = list.length;
                that.loading2 = true;
            },

            /**
             * 上传前
             */
            before_upload(e) {
                let that = this;
                // console.log(e);
                that.postData.type = e.type;
            }
        }
    };
</script>

