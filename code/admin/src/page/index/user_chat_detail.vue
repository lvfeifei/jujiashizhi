<template>
<div style="padding-bottom: 30px;" id="subpage">
  <el-breadcrumb separator="/">
    <el-breadcrumb-item>
      <b>首页</b>
    </el-breadcrumb-item>
    <el-breadcrumb-item :to="{path: '/index/user_chat'}">
      <b>专家互动列表</b>
    </el-breadcrumb-item>
    <el-breadcrumb-item>互动详情</el-breadcrumb-item>
  </el-breadcrumb>
 
  <div class="content_wrapper">
    
    <div class="content mb30 content_left">
    <div v-if="user_info.id" class="xcx-head" style="padding:20px 25px 20px 20px;height:auto;margin-bottom:0">
      <div class="user_info_box">
        <div class="user_avatar">
          <img :src="user_info.avatar_url" />
        </div>
        <div class="user_box">
          <div class="user_name"> 
            <!-- {{ user_info.nickname }}  -->
            {{ '用户编号：' + user_info.id }} 
          </div> 
           <div class="hzxx_text">({{ user_info.patient_text }})</div>   
        </div>
      </div>

      <div class="btn_group_box">
        <el-button type="primary" @click="refersh">刷新</el-button>
        <el-button @click="back()">返回</el-button>
      </div>

    </div>

    <div class="xcx-content" style="padding: 0;">
      <div class="chat" id="chat">
        <template v-for="chat in chat_list">
          <!-- 时间 -->
          <div class="time_li" v-if="chat.msg_type==0">
            <div class="times">{{ chat.content }}</div>
          </div>

          <!-- 左侧消息 -->
          <div class="chat_left" v-if="chat.type==1 && chat.msg_type!=0">
            <div class="left_avatar"> 
                  <img  class="left_avatar_img" :src="chat.avatar_url" /> 
            </div>
 
            <div class="left_cont " v-if="chat.msg_type==1">{{ chat.content }}</div>
            <div class="left_cont chat_cont_audio" :style="{width: chat.voice_time * 15 + 'px'}"  @click="play_audio(chat.content)" v-if="chat.msg_type==3">
                 <div class="chat_con_audio_time">{{chat.voice_time + '”'}}</div><img  class="chat_con_audio_icon"  src="../../../static/img/play_icon.png" /> 
                 <audio :ref="chat.content"> 
                  <source :src="chat.content" type="audio/mpeg"> 
                </audio> 
            </div>

            <div class="triangle_left" v-if="chat.msg_type==2">
              <el-image style="max-width:300px" :src="chat.content" :preview-src-list="[chat.content]"></el-image>
            </div>
          </div>

          <!-- 右侧消息 -->
          <div class="chat_right" v-if="chat.type==2 && chat.msg_type!=0">
            <div class="right_avatar">
              <!-- <el-avatar style="background:#fff;" :src="chat.avatar_url"></el-avatar> -->
                <img  class="right_avatar_img" :src="chat.avatar_url" /> 
            </div>
            <div class="right_cont" v-if="chat.msg_type==1">
              <div v-html="chat.content"></div>
            </div>
            <div class="triangle_right" v-if="chat.msg_type==2">  
              <img  class="chat_pic" :src="chat.content" /> 
              <!-- <el-image  class="chat_pic" :src="chat.content" :preview-src-list="[chat.content]"></el-image> -->
            </div>

          

            <!-- 处理结果 -->
            <div class="processing_result" v-if="chat.msg_type==3" @click="go_problem_detail(chat.content.problem_id)">
              <div class="result_tit">
                处理结果：
                <span>已处理</span>
              </div>
              <div class="result_txt">投诉问题：{{chat.content.title}}</div>
              <div class="result_txt">处理日期：{{ chat.content.time }}</div>
            </div>
            <!-- <div class="right_img"><img src="" alt=""></div> -->
          </div>
        </template>
      </div>


    </div>
 
    <div class="xcx-footer">
      <div class="imgs">
        <div class="imgs_li">
          <el-upload class="updata_img"  :headers="upload_header" accept="image/*" :action="upload_img_url" :on-success="send_pic" :data="postData" :show-file-list="false" style="width:100px">
            <img style="width:22px; height:22px; float:left;margin-right:5px;" src="../../../static/img/index/add_pictrue.png" alt />
            发送图片
          </el-upload>
        </div>
         <!-- <div class="imgs_li" style="width:160px" @click="add_problem()">
          <img src="../../../static/img/index/add_txt.png" alt style="width:22px; height:22px; float:left;margin-right:5px;" />
          添加投诉处理结果
        </div> -->
      </div>
      <div class="chat_textarea">
        <el-input :rows="5" v-model="content" type="textarea" placeholder="请输入对话…"></el-input>
      </div>
      <div class="fun foter_bottom">
        <el-button style="width:120px" type="primary" @click="send_content()">发送</el-button>
        <el-button class="mar_R_10" @click="content=''" plain>清空</el-button>
      </div>
    </div>
    </div>

    <!-- 右边部分 -->
    <div class="content_right">
        <el-card>
            <h4>问题推荐方案选择</h4>

            <div class="search_box">
                <div class="search_left_box">
                    <el-select
                      v-model="question_value"
                      multiple
                      filterable 
                      default-first-option
                      placeholder="请选择问题">
                      <el-option
                        v-for="item in question_list"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id">
                      </el-option>
                    </el-select>
                </div>
                <div class="search_right_box">
                  <el-button @click="search_question"  type="primary">搜索</el-button>
                </div>
            </div>

            <div class="question_box">
               <!-- show-checkbox -->
              <!-- <el-tree
              :data="advice_list" 
              node-key="id"
              default-expand-all
              :expand-on-click-node="false">
              <span class="custom-tree-node" slot-scope="{ node, data }">  
               
                <div class="two_content_box">
                    <span  >{{ node.label}} </span>  
                    <el-button
                      type="text"
                      size="mini"
                      @click="() => append(data)">
                      添加
                    </el-button>   
                </div>  
              </span>

              </el-tree> -->

              <div class="answer_box" >  

                <div class="inner" v-for="item in advice_list" :key="item.pid">
                    
                    <div class="one_title_box">  
                      <div class="one_title_text">{{item.content}} </div> 
                      <el-button type="primary" size="mini" @click="one_title_text_click(item.advice)">添加</el-button> 
                    </div> 
 
                    <div class="two_title_box" v-for="advices in item.advice" :key="advices.id">  
                      <div class="two_title_text" style="display: flex;" v-for="(content,c_index) in advices.content" :key="c_index"> 
                         <span  v-if="content.type === 'text'">{{content.con}}</span> 
                         <img class="content_img" v-if="content.type === 'image'" :src="content.con" /> 
                         <el-button type="primary" size="mini" v-if="content.type === 'image'" @click="two_title_text_click(content.con,content.type)">发送</el-button>   
                         <el-button type="primary" size="mini" v-if="content.type === 'text'" @click="two_title_text_click(content.con,content.type)">添加</el-button>   
                      </div>  
                    </div>  
                    
                </div>
                
              </div>
 
            </div>
        </el-card>
    </div>

  </div>
 

</div>
</template>
<script src="../../../static/js/index/user_chat_detail.js"></script>
<style>
 
/* 新样式开始 */
.inner {
  margin-bottom: 20px;
}

.content_img {
  width: 200px;
  height: 50px;
  margin: 10px;
  vertical-align: middle;
}

.answer_box .one_title_box {
  display: flex;  
}

.answer_box .two_title_box {
  margin-top: 10px;
}

.one_title_text {
  margin-right: 10px;
}

.answer_box .two_title_box .two_title_text{ 
  font-size: 12px;
  padding-left: 20px;
  margin: 10px;
}

.el-tree-node__expand-icon {
  display: none;
} 

.question_box {
  margin-top: 20px;
}

.content_wrapper,
.search_box {
  display: flex; 
}

.content_left {
  flex: 6;
}

.content_right {
  flex: 4;
  margin-left: 15px;
  height: 679px; 
}

.el-card,
.el-card__body {
  height: 100%; 
}

.el-card {
  padding-bottom: 20px;
}

.el-card__body {
 overflow-y: auto;
}

.search_box {
  margin-top: 10px;
}
 
.search_left_box {
  flex: 1;
}
.search_right_box {
  margin-left: 10px;
}

.el-select {
  width: 100%;
}

.el-tag--small {
  white-space: normal;
  word-break: break-all;
  height: auto;
}

/* 新样式结束 */

.chat_cont_audio {
  background-color: #0486fe !important;
  position: relative; 
  /* width: 60px; */
  display: flex;
  cursor: pointer;
}


.chat_con_audio_time {
  font-size: 23px;
  font-family: PingFangSC-Medium, PingFang SC;
  font-weight: 500;
  color: #FFFFFF; 
  /* margin-right: 20px; */
}

/* .chat_con_audio_time::after{ 
  display: inline-block;
  content: '”';
} */

.chat_con_audio_icon {
  width: 19px;
  height: 25px; 
  transform: rotate(180deg); 
}

.foter_bottom {
  margin: 0 30px;
  display: flex;
  flex-direction: row-reverse;
  padding: 20px 0;
}

.chat_textarea {
  margin: 0 30px;
  width: auto;
}

.imgs .imgs_li+.imgs_li {
  margin-left: 30px;
}

.imgs .imgs_li {
  display: inline-block;
  width: 120px;
  height: 22px;
  cursor: pointer;
}

.imgs {
  display: flex;
  align-items: center;
  padding: 20px 0 20px 30px;
}

.chat_pic,
.chat_pic img {
  max-width:200px;
}

.imgs img {
  width: 100%;
  height: 100%;
  display: block;
}

.processing_result .result_txt+.result_txt {
  margin-top: 5px;
}

.processing_result .result_txt {
  font-size: 12px;
  color: #959a9f;
}

.processing_result .result_tit span {
  color: #08c790;
}

.processing_result .result_tit {
  font-size: 14px;
  color: #606266;
  margin-bottom: 8px;
}

.processing_result {
  width: 600px;
  padding: 13px 15px 12px 15px;
  background: #f9f9fa;
  border-radius: 4px;
  cursor: pointer;
  /* margin: 0 auto; */
}

.chat_right .right_img {
  width: 247px;
  height: 163px;
  background: antiquewhite;
  border-radius: 4px;
  overflow: hidden;
}

.chat_right .right_cont .triangle_right {
  width: 7px;
  height: 12px;
  position: absolute;
  right: -7px;
  top: 14px;
}

.chat_right .right_cont {
  background: #0486fe;
  padding: 10px 20px 10px 23px;
  font-size: 14px;
  color: #ffffff;
  border-radius: 4px;
  max-width: 420px;
  position: relative;
}

.chat_right .right_avatar .right_avatar_img{
  width: 40px;
  height: 40px;
  overflow: hidden;
  border-radius: 50%;
  background: antiquewhite;
  margin-left: 20px;
}

.chat_right {
  display: flex;
  flex-direction: row-reverse;
  margin-bottom: 20px;
}

.chat_left .left_img {
  width: 247px;
  height: 163px;
  background: antiquewhite;
  border-radius: 4px;
  overflow: hidden;
}

.chat_left .left_cont .triangle_left {
  width: 7px;
  height: 12px;
  position: absolute;
  left: -7px;
  top: 13px;
}

.chat_left .left_cont {
  background: #f0f2f6;
  padding: 10px 20px 10px 23px;
  font-size: 14px;
  color: #323232;
  border-radius: 4px;
  max-width: 420px;
  position: relative;
}

.chat_left .left_avatar .left_avatar_img{
  width: 40px;
  height: 40px;
  overflow: hidden;
  border-radius: 50%;
  background: antiquewhite;
  margin-right: 20px;
}

.chat_left {
  display: flex;
  margin-bottom: 20px;
}

.chat img {
  /* width: 100%; */
  /* height: 100%; */
  display: block;
}

.chat .time_li .times {
  padding: 4px 8px;
  background: #f0f2f6;
  border-radius: 3px;
  font-size: 12px;
  color: #959a9f;
}

.chat .time_li {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.chat {
  width: auto;
  padding: 20px 25px 40px;
  /* height: 350px; */
  height: 300px;
  overflow: hidden;
  overflow-y: scroll;
  border-bottom: 2px solid #ebeef5;
}

.user_info_box {
  display: flex;
  height: 50px;
  align-items: center;
}

.user_info_box .user_avatar {
  width: 50px;
  height: 50px;
  border-radius: 5.36px;
  overflow: hidden;
  background: antiquewhite;
  margin-right: 10px;
  flex-shrink: 0;
}

.user_info_box .user_avatar img {
  width: 100%;
  height: 100%;
  display: block;
}

.btn_group_box {
  flex-shrink: 0;
  margin-right: 15px;
}

.hzxx_text {
  font-size: 12px;
  color:#666;
  margin-right: 10px;
  padding-bottom: 10px;
}

.user_info_box .user_box {
  /* height: 50px; */
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.user_info_box .user_box .user_name {
  font-size: 20px;
  color: #000000;
  font-weight: 800;
}

.user_info_box .user_box .user_phone {
  font-size: 14px;
  color: #959a9f;
}
</style>
