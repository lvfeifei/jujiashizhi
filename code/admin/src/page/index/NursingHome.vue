<template>
  <div id="order_detail">
    <el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item style="margin-right:0;"><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item >养老院主页</el-breadcrumb-item>
				 
			</el-breadcrumb>
		</el-col>
    <!--基本信息-->
    
    <div class="model">
      <div class="top_box" style="display: flex">
        <div class="content2" style="background: #fff; margin-right: 20px">
          <div class="head">养老院主页</div>
          <div style="padding: 0 20px">
            <!-- <p style="font-size:14px;color:#303133;"><span>照护者ID：</span>{{detail_data.id}}</p> -->
            <p style="font-size: 14px; color: #303133">
              <span>邀请码:</span>
              <a :href="detail_data.invitation_code" target="_blank" rel="noopener noreferrer">
               <img class="user_invite_img"  :width="invitation_code_width" :src="detail_data.invitation_code" alt="" />
              </a>
             
            </p>
            <div style="padding-left:100px;margin-bottom:50px;">
               <el-button size="mini" type="primary" style="text-align: center;" @click="invitation_code_width += 20">放大</el-button >
               <el-button size="mini" type="primary" style="text-align: center;" @click="down_invitation_code">下载</el-button >
            </div>
             <p style="font-size: 14px; color: #303133">
              <span>养老院logo:</span>
              <img class="user_avatar" :src="detail_data.logo" alt="" />
            </p>

            <p style="font-size: 14px; color: #303133">
              <span>养老院名称：</span>{{detail_data.title}}
            </p>
            <p style="font-size: 14px; color: #303133">
              <span>养老院地址：</span>{{detail_data.address}}
            </p>
            <p style="font-size: 14px; color: #303133">
              <span>联系人姓名：</span>{{detail_data.name}}
            </p>
            <p style="font-size: 14px; color: #303133">
              <span>联系人电话：</span>{{detail_data.mobile}}
            </p>
            <p style="font-size: 14px; color: #303133">
              <span style="margin-right: 56px">登录账号：</span>{{detail_data.username}}
            </p>
            <p style="font-size: 14px; color: #303133">
              <span style="margin-right: 56px">合作状态： </span >
              <span v-if="detail_data.status == 1">合作中</span>  
              <span v-if="detail_data.status == 2">已终止</span>  
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<!-- <script type="text/javascript">
export default {
  data() {
    return {
      radio: "1",
    };
  },
};
</script> -->
<script type="text/javascript" src="../../../static/js/index/NursingHome.js"></script>
<style lang="less" scoped>
/deep/.el-radio__input.is-checked + .el-radio__label {
  color: #000;
}
span {
  margin-right: 40px;
}
.signPic {
  width: 200px;
  height: 100px;
  margin-right: 10px;
}
.floorPic {
  width: 100px;
  height: 100px;
  margin-right: 40px;
}
.dianwei_modual {
  font-size: 14px;
}
/* .dianwei_modual .dianwei_content{
	display: flex;
	justify-content: space-between;
} */
.dianwei_modual .title {
  margin: 32px 0;
}
#order_detail .dianwei_modual .dianwei_content .dianwei1 {
  margin: 32px 0 0 0;
}
#order_detail .dianwei_modual .dianwei_content .dianwei1 .pre_txt {
  margin-left: 10px;
}
#order_detail .dianwei_modual .zhujuPic {
  width: 100px;
  height: 100px;
  margin-right: 20px;
}
#order_detail .dianwei_modual .zhuju_modual {
  display: flex;
  align-items: center;
}
#order_detail .dianwei_modual .zhuju_modual p {
  line-height: 32px;
  margin: 0;
}
#order_detail .backABtn {
  margin: 30px 0 0 20px;
}

.user_avatar {
  width: 100px;
  height: 100px;
  // border-radius: 50%;
  vertical-align: middle;
}

.user_invite_img {
  //  width:160px;
   vertical-align: middle;
}
</style>
