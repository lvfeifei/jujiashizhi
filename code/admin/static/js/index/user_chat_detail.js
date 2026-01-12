let timer = null;
import commJS from '../common.js';

const data = [{
    id: 1,
    label: '一级 1',
    children: [{
      id: 4,
      label: '二级 1-1' 
    }]
  }, {
    id: 2,
    label: '一级 2',
    children: [{
      id: 5,
      label: '二级 2-1'
    }, {
      id: 6,
      label: '二级 2-2'
    }]
  }, {
    id: 3,
    label: '一级 3',
    children: [{
      id: 7,
      label: '二级 3-1'
    }, {
      id: 8,
      label: '二级 3-2'
    }]
  }];


export default {
    data() {
        return {
            user_id: '',
            user_info: {},
            chat_list: [],

            manger_id: '',          // 系统管理员
            content: '',            // 回复内容
            upload_header:{
				token:this.cookie.get('token')
			},
            upload_img_url: this.adminApi.upload_url,       // 上传图片
            postData: {folder:"chat"},                   // 上传数据
            domain: '',
            last_audio:'',
            timer:null,  
            value: [],
            question_list:[],  // 问题选项
            question_value:[], // 问题选项的id
            advice_list:[],    // 问题答案的列表
            data: JSON.parse(JSON.stringify(data)),
            last_length:0,
            is_update:true
        }
    },

    updated() {
        if(this.is_update){ 
            // 聊天定位到底部
            let ele = document.getElementById('chat');
            ele.scrollTop = ele.scrollHeight;
        } 
    },

    /**
     * 进入页面加载
     */
    mounted: function () {
 
        var that = this;
        that.token = sessionStorage.getItem('access-token');
        // commJS.getQiNiuToken(that);

        // 获取当前管理员ID
        that.manger_id = sessionStorage.getItem('user_id');

        // 获取对话用户ID
        var query = that.$route.query;
        if (query.user_id) {
            that.user_id = query.user_id;
            that.timer = setInterval(()=>{
                that.getDetail();
            },3000)
           
        }

        // 获取问题选项
        this.getQuestionList()

    },

    //方法
    methods: {

        // 搜索答案  
        search_question(){
            let ids = this.question_value
            if(!ids.length){
                return this.$message.warning('请选择输入的问题!');
            } 
            this.axios.post("/care_advice/advice",{
                ids,
                user_id:this.user_id
            },{
                emulateJSON: true
            }).then(data => { 
                console.log(data) 
                if(data.status === 1){ 
                    let advice_list = data.data  
                    console.log('advice_list:', advice_list)
                    advice_list.forEach(item => {
                        item.label = item.content
                        item.id = item.id
                        if(item.advice.length){
                            item.advice.forEach(advices => { 
                                advices.label = advices.content
                            })
                        } 
                        item.children = item.advices
                    }) 
                    // console.log('advice_list_forEach',advice_list)

                    console.log(advice_list)
                    this.advice_list = advice_list
                } 
            }); 
    
        },
      
        /**
         * 获取问题选项列表
        */
        getQuestionList() {
            var that = this; 
            that.axios.post("/evaluation_capability/question",{
                emulateJSON: true
            }).then(data => {  
                if(data.status === 1){
                    let list = data.data 
                    this.question_list = list
                } 
            });
        },

        // 一级标题点击
        one_title_text_click(advice){
            // console.log(advice)
            let str = ''
            advice.forEach(item => { 
                item.content.forEach(content_item => {
                    if(content_item.type === 'text') {
                        str += content_item.con
                    }
                });
            });
            this.content += str  
        },

        // 发送 二级标题 内容
        /**  判断类型  
         *   是图片就提示是否发送 
         *   是内容就直接添加到输入框
         * */
        two_title_text_click(con,type){
           if(type === 'text'){ 
             this.content  = this.content + con  + ", " + ' ' 
           }else if(type === 'image'){ 
            this.$confirm('确实要发送图片吗?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => { 
				// 发送图片
                this.send_data(2, con);
			}).catch(); 
           }
        },

        // 播放音频
        play_audio(ref){ 
           if(this.last_audio){  
                this.$refs[this.last_audio][0].pause();
            } 
            this.last_audio = ref 
            this.$refs[ref][0].play()
        },

        refersh() {
            var that = this;
            that.getDetail();
            return that.$message.success('刷新成功');
        },

        /**
         * 发送图片
         * @param {} res 
         */
        send_pic: function (res) {
            console.log(res);
            var that = this; 
            // 发送数据
            that.send_data(2, res.data.imgurl);
        },

        /**
         * 发送-文本内容
         */
        send_content() {
            var that = this;

            if (!that.content) {
                return that.$message.warning('请填写内容!');
            }

            // 发送数据
            that.send_data(1, that.content);
        },



        /**
         * 发送数据
         * @param {*} type 
         * @param {*} content 
         */
        send_data: function (type, content) {
            var that = this;
            that.axios.post("/User_chat/reply_user", {
                token: that.token,
                user_id: that.user_id,
                manger_id: that.manger_id,
                msg_type: type,
                content: content
            }, {
                emulateJSON: true
            }).then(res => {
                console.log(res)
                // if (res.status == 200) {
                //     that.content = '';
                //     that.getDetail();
                // } else {
                //     return that.$message.warning(res.data);
                // }

                if(type == 1){
                    that.content = '';
                } 
                that.getDetail();
            });
        },

        /**
         * 获取详情
         */
        getDetail() {
            var that = this;
            //滚动到顶部
            // document.getElementById("subpage").scrollIntoView();
            that.axios.post("/User_chat/chart_list", {
                token: that.token,
                user_id: that.user_id
            }, {
                emulateJSON: true
            }).then(data => { 
                if (data) {
                    that.user_info = data.user_detail;
                    that.chat_list = data.list; 
                    if(that.last_length && that.last_length == that.chat_list.length){
                        that.is_update = false
                    }else{
                        that.last_length = that.chat_list.length
                        that.is_update = true
                    }  
                    // that.updated();
                }
            });
        },

        /**
         * 返回
         */
        back() {
            this.$router.push({
                path: '/index/user_chat'
            });
        },

        /**
         * 添加-处理问题
         */
        add_problem() {
            this.$router.push({
                path: '/complaint/add_problem',
                query: {
                    user_id: this.user_id
                }
            });
        },

        /**
         * 进入 - 处理问题详情页面
         * @param {*} id 
         */
        go_problem_detail(id) {
            this.$router.push({
                path: '/complaint/problem_detail',
                query: {
                    id: id
                }
            });
        },

        /**
         * 详情
         */
        to_detail(id) {
            var that = this;
            that.user_id = id;
            that.getDetail();
        }
    },

    destroyed() {
        clearInterval(this.timer)
    },
}
