<template>
    <div>  
      <ckeditor :editor="editor" v-model="describe" :config="editorConfig"  :disabled="disabled" @input="onEditorChange"></ckeditor>
    </div>
</template>
<script >
import commJS from "../../../static/js/common"; 
 
export default {
    name: "richText", 
    data() {
        return {
            editor: 'ClassicEditor',
            describe: '<p>请输入内容...</p>',
            editorConfig: {
                // The configuration of the editor.
                // width:800,
                height:400,
                toolbarGroups:[
                    { name: 'clipboard',groups: [ 'clipboard', 'undo'] },
                    { name: 'editing',groups: [ 'find', 'selection', 'spellchecker' ] },
                    { name: 'links' },
                    { name: 'insert' },
                    { name: 'forms' },
                    { name: 'tools' },
                    { name: 'document',groups: [ 'mode', 'document', 'doctools' ] },
                    { name: 'others' },
                    { name: 'basicstyles',groups: [ 'basicstyles', 'cleanup' ] },
                    { name: 'paragraph',groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                    {
                        name: 'styles',
                        groups: ['Format','Font', 'FontSize']
                    },
                    { name: 'Font' },
                    { name: 'colors' },
                ],
                toolbarCanCollapse:true,
                resize_enabled:false,
                toolbarStartupExpanded:true,
                pasteFromWordRemoveStyles:false,
                allowedContent:true,
                // resize_minWidth:600,
                // resize_maxWidth:200
                enterMode:'CKEDITOR.ENTER_P',
                toolbarLocation:'top',
                language:'zh-cn',
                contentsLangDirection:'ltr', //从左到右
                image_previewText:' ',
                removeDialogTabs:'image:advanced;image:Link;',
                filebrowserImageUploadUrl:'',
                filebrowserUploadUrl:'',
                extraPlugins:'button,panelbutton,colorbutton,richcombo',
                format_tags:'p;h1;h2;h3;pre',
                removeButtons:'Source,Copy,Cut,Save,Paste,PasteText,PasteFromWord,Print,SpellChecker,Scayt,about',
                extraPlugins:'image2,colorbutton',
                // mathJaxLib:'//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML',
            }

        };
    },

    props: {
        describe: "",
        disabled: "",
        total_count:'',
    },

    /**
     * 创建前
     */
    created() {
        var that = this;
        // that.editorConfig.filebrowserImageUploadUrl = that.adminApi.admin_api + '/index/ck_upload';
        // that.editorConfig.filebrowserUploadUrl = that.adminApi.admin_api + '/index/ck_upload'; 
        that.editorConfig.filebrowserImageUploadUrl = this.adminApi.upload_url
        that.editorConfig.filebrowserUploadUrl = this.adminApi.upload_url
        
    },

    /**
     * 加载后
     */
    mounted: function() {
        var that = this; 
    },
    //方法
    methods: {
        // 富文本编辑器配置
        onEditorChange(e) {
            var that = this; 
            that.$emit("editor_change", this.describe);
        },
    }
};
</script>

