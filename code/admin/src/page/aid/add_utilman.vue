<template>
    <div id="add_shop">
        <el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>助具</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/aid/utilman_manage">助具管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{banner_id ? '编辑' : '新增'}}助具</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
      <el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 100%;">
        <!--基本信息-->
        <div class="model">
            <div class="content">
                <div class="head">基本信息</div>
                <div class="rebate">
                  <el-form-item label="助具名称：" prop="title">
                    <el-input class="w400" v-model="form_data.title" placeholder="请输入助具名称"></el-input>
                  </el-form-item>
                  <el-form-item label="助具简介：" prop="content">
                    <el-input class="w400" v-model="form_data.content" placeholder="请输入助具简介" type="textarea"></el-input>
                  </el-form-item>
                  <el-form-item label="助具单价：" prop="price">
                    <el-input class="w400" v-model="form_data.price" placeholder="请输入助具单价"></el-input>
                  </el-form-item>
                  <el-form-item label="助具描述：" prop="describe">
                    <richText placeholder='请输入内容' :disabled="false" @editor_change='editor_change' ref="richText" :describe='form_data.describe'></richText>
                  </el-form-item>
                  <el-form-item label="关联类目：" prop="class_id">
                    <el-select clearable style="width:240px;" v-model="form_data.class_id" placeholder="请选择关联类目" class="title mar_R_10" >
                      <el-option v-for="item in classData" :key="item.id" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                  </el-form-item>
                  <el-form-item label="上传图片：" prop="picture">
                    <el-upload class="updata_img" accept="image/*" :on-exceed='descExceed' :limit="1" :on-remove='del_img' :action="upload_img_url" :headers="uploadToken" :file-list="form_data.picture" list-type="picture-card" :on-success="img_succ" :data="postData">
                      <i class="el-icon-plus"></i>
                      <div style="font-size: 12px; color: #606266;" slot="tip">图片大小（360px * 360px）像素，图片格式png、jpg</div>
                    </el-upload>
                  </el-form-item>
                </div>
              <div class="head">助具规格</div>
              <div class="one-table mar_T_20">
                <el-button type="primary" @click="dialogVisible = true">添加规格</el-button>
              </div>
              <div id="list">
                <el-table border :data="class_table" style="width: 100%" class="mar_B_30">
                  <el-table-column fixed prop="name" label="规格名称"></el-table-column>
                  <el-table-column prop="price" label="规格价格"></el-table-column>
                  <el-table-column prop="model_url" label="模型AR地址"></el-table-column>
                  <el-table-column prop="picture" label="规格图片">
                    <div slot-scope="scope">
                      <img class="specImg" src="../../../img/1.png" alt="">
                    </div>
                  </el-table-column>
                  <el-table-column label="操作">
                    <template slot-scope="scope">
                      <el-button @click="edit(scope.$index)" type="text" size="small">编辑</el-button>
                      <el-button
                        @click.native.prevent="deleteRow(scope.$index, class_table)"
                        type="text"
                        size="small"
                        style="color:red;"
                      >移除</el-button>
                    </template>
                  </el-table-column>
                </el-table>
              </div>
              <el-form-item class="mar_T_50">
                <el-button type="primary" @click="save()">确认</el-button>
                <el-button @click="back">返回</el-button>
              </el-form-item>
            </div>

        </div>

      </el-form>
        <!--添加规格-->
        <el-dialog
            title="添加规格"
            :visible.sync="dialogVisible"
            width="30%"
            :before-close="close_table"
        >
            <el-form rel="add_spec" class="demo-ruleForm" label-width="84px">
                <el-form-item label="规格名称：" prop="OldPass">
                    <el-input placeholder="请输入规格名称" v-model="class_name" clearable></el-input>
                </el-form-item>
                <el-form-item label="规格价格：" prop="pass">
                    <el-input placeholder="请输入规格价格" v-model="class_price" clearable></el-input>
                </el-form-item>
                <el-form-item label="模型AR地址：" prop="model_url">
                    <el-input placeholder="请输入模型AR地址" v-model="class_model_url" clearable></el-input>
                </el-form-item>
                <el-form-item label="规格图片：" prop="picture">
                        <el-upload class="unilman_img" accept="image/*" :on-exceed='descExceed' :limit="1" :on-remove='del_img' :action="upload_img_url" :file-list="picture" list-type="picture-card" :on-success="img_succ" :data="postData">
                                <i class="el-icon-plus"></i>
                                <div style="font-size: 12px; color: #606266;" slot="tip">图片大小（360px * 360px）像素，图片格式png、jpg</div>
                        </el-upload>
                </el-form-item>
                <el-form-item class="dialog-footer">
                    <el-button @click="dialogVisible = false">取 消</el-button>
                    <el-button type="primary" @click="confirm">确 定</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>

        <!--修改规格-->
        <el-dialog
            title="修改规格"
            :visible.sync="dialogVisible2"
            width="30%"
            :before-close="close_table"
        >
            <el-form class="demo-ruleForm" label-width="110px">
                <el-form-item label="规格名称：" prop="OldPass">
                    <el-input placeholder="请输入规格名称" v-model="class_name" clearable></el-input>
                </el-form-item>
                <el-form-item label="规格价格：" prop="pass">
                    <el-input placeholder="请输入规格价格" v-model="class_price" clearable></el-input>
                </el-form-item>
                <el-form-item label="模型AR地址：" prop="pass">
                    <el-input placeholder="请输入模型AR地址" v-model="class_model_url" clearable></el-input>
                </el-form-item>
                <el-form-item label="规格图片：" prop="picture">
                        <el-upload class="unilman_img" accept="image/*" :on-exceed='descExceed' :limit="1" :on-remove='del_img' :action="upload_img_url" :file-list="picture" list-type="picture-card" :on-success="img_succ" :data="postData">
                                <i class="el-icon-plus"></i>
                                <div style="font-size: 12px; color: #606266;" slot="tip">图片大小（360px * 360px）像素，图片格式png、jpg</div>
                        </el-upload>
                </el-form-item>

                <el-form-item class="dialog-footer">
                    <el-button @click="dialogVisible2 = false">取 消</el-button>
                    <el-button type="primary" @click="confirm2">确认</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>

<script type="text/javascript" src="../../../static/js/aid/add_utilman.js"></script>

<style scoped>
    @import "../../../static/css/global.css"; /*引入公共样式*/
    #add_shop .unilman_img .el-upload.el-upload--picture-card{
        width: 100px;
        height: 100px;
    }
    #add_shop .unilman_img{
        display: inline-block;
    }
    #add_shop .specImg{
        width: 80px;
        height: 80px;
    }
</style>
