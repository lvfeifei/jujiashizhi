import richText from "../../src/page/common/richText";
import richText_two from "../../src/page/common/richText_two";
export default {
	components: {
		richText,
		richText_two
	},
	data() {
		return {
			is_switch_distribution: false,
			form_data: {
				is_one_level_distribution: false,
				one_level_distribution_type: '1',
				one_level_distribution: '',
				is_two_level_distribution: false,
				two_level_distribution_type: '1',
				two_level_distribution: '',
				is_three_level_distribution: false,
				three_level_distribution_type: '1',
				three_level_distribution: '',
				distribution_content: '',
				distribution_rule: '',
				distribution_settlement_type: '1',
			},
			rules: {
				one_level_distribution: [{
					required: true,
					message: '请输入一级分销佣金',
					trigger: 'blur',
				}],
				two_level_distribution: [{
					required: true,
					message: '请输入二级分销佣金',
					trigger: 'blur'
				}],
				three_level_distribution: [{
					required: true,
					message: '请输入三级分销佣金',
					trigger: 'blur'
				}],
				distribution_content: [{
					required: true,
					message: '请输入申请分销介绍',
					trigger: 'blur'
				}],

				distribution_rule: [{
					required: true,
					message: '请输入分销规则',
					trigger: 'blur'
				}],
			},

			token: '',
		}
	},

	/**
	 * 
	 */
	mounted() {
		var that = this;
		
		that.get_detail();
	},

	/**
	 * 方法
	 */
	methods: {

		/**
		 * 获取详情
		 */
		get_detail() {
			var that = this;
			that.axios.post("/config/show_distribution", {
				token: that.token,
			}, {
				emulateJSON: true
			}).then(res => {
				var data = res;
				if (data) {
					that.is_switch_distribution = data.is_switch_distribution == 1 ? true : false;
					that.form_data.is_one_level_distribution = data.is_one_level_distribution == 1 ? true : false;
					that.form_data.one_level_distribution_type = data.one_level_distribution_type.toString();
					that.form_data.one_level_distribution = data.one_level_distribution;
					that.form_data.is_two_level_distribution = data.is_two_level_distribution == 1 ? true : false;
					that.form_data.two_level_distribution_type = data.two_level_distribution_type.toString();
					that.form_data.two_level_distribution = data.two_level_distribution;
					that.form_data.is_three_level_distribution = data.is_three_level_distribution == 1 ? true : false;
					that.form_data.three_level_distribution_type = data.three_level_distribution_type.toString();
					that.form_data.three_level_distribution = data.three_level_distribution;
					that.form_data.distribution_content = data.distribution_content;
					that.form_data.distribution_rule = data.distribution_rule;
					that.form_data.distribution_settlement_type = data.distribution_settlement_type.toString();
				}
			});
		},


		/**
		 * 富文本改变时
		 * @param {*} currentPage 
		 */
		editor_change(e) {
			this.form_data.distribution_content = e;
		},

		/**
		 * 富文本two改变时
		 * @param {*} e 
		 */
		distribution_rule_change(e) {
			this.form_data.distribution_rule = e;
		},

		/**
		 * 保存
		 */
		save(formName){
			const that = this;
			that.$refs[formName].validate((valid) => {
			  if (!valid) return that.$message.warning('请完整填写内容!');
			  var formData = {};
			  formData = that.form_data;
			  formData.is_switch_distribution = that.is_switch_distribution ? 1 :2;
			  formData.is_one_level_distribution = that.form_data.is_one_level_distribution ? 1 :2;
			  formData.is_two_level_distribution = that.form_data.is_two_level_distribution ? 1 :2;
			  formData.is_three_level_distribution = that.form_data.is_three_level_distribution ? 1 :2;
			  var url = '/config/set_distribution';
			  that.axios.post(url, formData, {
				emulateJSON: true
			  }).then(() => {
				that.$message.success('保存成功');
				that.get_detail();
			  });
			})
		}
	}
}
