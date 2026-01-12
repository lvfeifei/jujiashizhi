<template>
    <div class="line_nor">
        <quill-editor v-model="describe_two" v-loading="loading_two" element-loading-text="上传中..." :disabled='disabled'
                      element-loading-spinner="el-icon-loading" ref="myQuillEditor_two" :options='editorOption_two'
                      class="quill-editor_two" @ready="onEditorReady_two($event)"
                      @change="onEditorChange_two($event)" @focus="onFocus_two($event)" @blur="onEditorBlur_two($event)"></quill-editor>

        <el-upload style="display:none;" multiple accept="image/*" :before-upload='before_upload_two'
                   :on-progress='img_progress_two' ref="upload_img_two" class="upload_img_two" :action="upload_img_two_url"
                   :show-file-list="false" :on-success="AvatarSuccess_two" :data="postData"></el-upload>

        <el-upload style="display:none;" accept="video/*" :before-upload='before_upload_two' :on-progress='img_progress_two'
                   ref="upload_video_two" class="upload_video_two" :action="upload_img_two_url" :show-file-list="false"
                   :on-success="AvatarSuccess_two" :data="postData"></el-upload>
    </div>
</template>

<script>
    import {quillEditor} from "vue-quill-editor";
    import Quill from "quill";
    import commJS from "../../../static/js/common";

    var toolbarOptions = [
        ["bold", "italic", "underline", "strike"],
        [{header: 1}, {header: 2}],
        [{list: "ordered"}, {list: "bullet"}],
        [{script: "sub"}, {script: "super"}],
        [{indent: "-1"}, {indent: "+1"}],
        [{direction: "rtl"}],
        [{size: ["small", false, "large", "huge"]}],
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
        name: "richText_two",
        data() {
            return {
                // 富文本编辑器配置
                editorOption_two: {
                    placeholder: "",
                    theme: "snow", //样式
                    modules: {
                        toolbar: {
                            container: toolbarOptions, //工具栏
                            handlers: {
                                image: function (value_two) {
                                    if (value_two) {
                                        document
                                            .querySelector(".upload_img_two input")
                                            .click();
                                    } else {
                                        this.quill.format("image", false);
                                    }
                                },
                                video: function (value_two) {
                                    if (value_two) {
                                        document
                                            .querySelector(".upload_video_two input")
                                            .click();
                                    } else {
                                        this.quill.format("video", false);
                                    }
                                }
                            }
                        }
                    }
                },
                loading_two: false,
                // 七牛云地址
                upload_img_two_url: this.adminApi.upload_url,
                postData: {},
                //图片域名
                domain: "",
                upload_length_two: ""
            };
        },

        props: {
            describe_two: "",
            placeholder: "",
            // 禁用
            disabled: true
        },

        /**
         * 创建前
         */
        created() {
            var that = this;
            that.editorOption_two.placeholder = that.placeholder;
        },

        /**
         * 加载后
         */
        mounted: function () {
            var that = this;
            
            commJS.getQiNiuToken(that);
        },

        //方法
        methods: {
            // 富文本编辑器配置
            onEditorChange_two(e) {
                var that = this;
                that.$emit("editor_change_two", e.html);
            },

            /**
             *富文本信息
             */
            onEditorReady_two(e) {
            },

            /**
             *获取焦点时间
             */
            onFocus_two(e){

            },

            /**
             * 失去焦点事件
             */
            onEditorBlur_two(e){

            },

            //上传成功
            AvatarSuccess_two(res, file, list) {
                //上传成功后在图片框显示图片
                let that = this;
                if(that.postData.type.indexOf("video") != -1 ){
                    that.loading_two = false;
                    // 获得文件上传后的URL地址
                    if (res) {
                        // 将文件上传后的URL地址插入到编辑器文本中
                        let value_two = that.domain + res.key;
                        // 获取光标位置对象，里面有两个属性，一个是index 还有 一个length，这里要用range.index，即当前光标之前的内容长度，然后再利用 insertEmbed(length, 'image', imageUrl)，插入图片即可。
                        let length_two = that.$refs.myQuillEditor_two.quill.getSelection().index;
                        that.addRange_two = that.$refs.myQuillEditor_two.quill.getSelection();
                        value_two = value_two.indexOf("http") !== -1 ? value_two : "http:" + value_two;

                        that.$refs.myQuillEditor_two.quill.insertEmbed(
                            that.addRange_two !== null ? that.addRange_two.index : 0,
                            // file.raw.type.split("/")[0] == "image" ? "image" : "video",
                            file.raw.type.split("/")[0],
                            value_two,
                            Quill.sources.USER
                        ); // 调用编辑器的 insertEmbed 方法，插入URL

                        // 调整光标到最后
                        that.$refs.myQuillEditor_two.quill.setSelection(length_two + 1);
                    } else {
                        that.$message.error(`${that.uploadType}插入失败`);
                    }
                    that.$refs["upload_img_two"].clearFiles(); // 插入成功后清除input的内容
                    that.$refs["upload_video_two"].clearFiles(); // 插入成功后清除input的内容
                }else {
                    let imgArr_two = [];
                    for (var j = 0; j < that.$refs.upload_img_two.uploadFiles.length; j++) {
                        if (that.$refs.upload_img_two.uploadFiles[j].status == "success") {
                            if (!that.$refs.upload_img_two.uploadFiles[j].filedId) {
                                imgArr_two.push(
                                    that.$refs.upload_img_two.uploadFiles[j].response
                                );
                            } else {
                                imgArr_two.push(
                                    that.$refs.upload_img_two.uploadFiles[j].filedId
                                );
                            }
                        }
                    }
                    if (imgArr_two.length == that.upload_length_two) {
                        imgArr_two.forEach(ele => {
                            that.loading_two = false;
                            // 获得文件上传后的URL地址
                            if (ele) {
                                // 将文件上传后的URL地址插入到编辑器文本中
                                let value_two = that.domain + ele.key;
                                // 获取光标位置对象，里面有两个属性，一个是index 还有 一个length，这里要用range.index，即当前光标之前的内容长度，然后再利用 insertEmbed(length, 'image', imageUrl)，插入图片即可。
                                let length_two = that.$refs.myQuillEditor_two.quill.getSelection()
                                    .index;
                                that.addRange_two = that.$refs.myQuillEditor_two.quill.getSelection();
                                value_two = value_two.indexOf("http") !== -1
                                    ? value_two
                                    : "http:" + value_two;

                                that.$refs.myQuillEditor_two.quill.insertEmbed(
                                    that.addRange_two !== null ? that.addRange_two.index : 0,
                                    // file.raw.type.split("/")[0] == "image" ? "image" : "video",
                                    file.raw.type.split("/")[0],
                                    value_two,
                                    Quill.sources.USER
                                ); // 调用编辑器的 insertEmbed 方法，插入URL

                                // 调整光标到最后
                                that.$refs.myQuillEditor_two.quill.setSelection(length_two + 1);
                            } else {
                                that.$message.error(`${that.uploadType}插入失败`);
                            }
                            that.$refs["upload_img_two"].clearFiles(); // 插入成功后清除input的内容
                            that.$refs["upload_video_two"].clearFiles(); // 插入成功后清除input的内容
                        });
                    }
                }
            },

            /**
             * 上传时
             */
            img_progress_two(res, file, list) {
                let that = this;
                that.upload_length_two = list.length;
                that.loading_two = true;
            },

            /**
             * 上传前
             */
            before_upload_two(e) {
                let that = this;
                that.postData.type = e.type;
            }
        }
    };
</script>
