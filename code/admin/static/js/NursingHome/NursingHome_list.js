import commJS from '../common.js'; 
export default {
    data() {
        return {
            
            dialogVisible: false,
            img_url: this.adminApi.img_url,
            sex_id: '0',

            sex_list: [
                {
                    id: 'S1',
                    name: '男'
                },
                {
                    id: 'S2',
                    name: '女'
                }
            ],
            xueli_id: '0',
            xueli_list: [
                {
                    id: 'U1',
                    name: '未上过学/不识字',
                },
                {
                    id: 'U2',
                    name: '小学',
                },
                {
                    id: 'U3',
                    name: '初中',
                },
                {
                    id: 'U4',
                    name: '高中/中专'
                },
                {
                    id: 'U5',
                    name: '本科及以上'
                }
            ],
            year_id: "0",
            year_list: [
                {
                    id: 'V1',
                    name: '<1年'
                },
                {
                    id: 'V2',
                    name: '1-2年'
                },
                {
                    id: 'V3',
                    name: '2–4年'
                },
                {
                    id: 'V4',
                    name: '>4年'
                }
            ],
            guanxi_id: "0",
            guanxi_list: [
                {
                    id: 'W1',
                    name: '配偶'
                },
                {
                    id: 'W2',
                    name: '子女'
                },
                {
                    id: 'W3',
                    name: '媳婿'
                },
                {
                    id: 'W4',
                    name: '其他'
                }
            ],
            room_id: "0",
            room_list: [
                {
                    id: 'X1',
                    name: '是'
                },
                {
                    id: 'X2',
                    name: '否'
                }
            ],
            status: 0,
            date: '',
            tableData: [],
            count: 0,
            page: 1,
            limit: 10,
            key: '',
            prevList: [],
            multipleSelection: [],
            project_type_list: [],
            dialog_visible_item:{}
        }
    },
    //进入页面加载
    mounted: function () {
        var that = this;
        that.getList(); 
    },

    //方法
    methods: { 
        downloadByBlob(url,name) {
            let image = new Image()
            image.setAttribute('crossOrigin', 'anonymous')
            image.src = url
            image.onload = () => {
              let canvas = document.createElement('canvas')
              canvas.width = image.width
              canvas.height = image.height
              let ctx = canvas.getContext('2d')
              ctx.drawImage(image, 0, 0, image.width, image.height)
              canvas.toBlob((blob) => {
                let url = URL.createObjectURL(blob)
                download(url,name)
                // 用完释放URL对象
                URL.revokeObjectURL(url)
              })
            }
          },
        download(href, name) {
            let eleLink = document.createElement('a')
            eleLink.download = name
            eleLink.href = href
            eleLink.click()
            eleLink.remove()
        },

        // 下载二维码
        down_invitation_code(){
            // console.log(this.dialog_visible_item.invitation_code) 
            // // this.downloadByBlob(this.dialog_visible_item.invitation_code,'66.png')
            // var a = document.createElement("a"); //创建一个<a></a>标签
            // a.href = this.dialog_visible_item.invitation_code; // 给a标签的href属性值加上地址，注意，这里是绝对路径，不用加 点.
            // a.download = "xxx.png"; //设置下载文件文件名，这里加上.xlsx指定文件类型，pdf文件就指定.fpd即可
            // a.style.display = "none"; // 障眼法藏起来a标签
            // document.body.appendChild(a); // 将a标签追加到文档对象中
            // a.click(); // 模拟点击了a标签，会触发a标签的href的读取，浏览器就会自动下载了
            // a.remove(); // 一次性的，用完就删除a标签
           let url = this.adminApi.api_url + '/bead_house/download_code?code_url=' + this.dialog_visible_item.invitation_code ;
           window.location.href = url
        },
         

        // 打开弹窗
        open_dialog(item){
            this.dialog_visible_item =  item
            this.dialogVisible = true 
        },
        handleClose(done) {
            this.$confirm('确认关闭？')
                .then(_ => {
                    done();
                })
                .catch(_ => { });
        },
    
        addstatus(e) {
            this.status = e
            this.page = 1;
            this.getList()
        },

        /**
         * 获取列表
         */
        getList() {
            let that = this;
            let formData = {};
            var curr_page = sessionStorage.getItem('curr_page');
            that.page = curr_page ? curr_page - 0 : that.page;
            formData.page = that.page;
            formData.limit = that.limit; 
            formData.status = that.status 
            if (that.key) {
                formData.key = that.key;
            } 
            that.axios.post("/bead_house/index", formData, {
                emulateJSON: true
            }).then(
                function (res) {
                    let data = res.data
                    if (data) {  
                        that.tableData = data.list; 
                        that.count = data.count;
                        that.page = curr_page ? curr_page : that.page;
                        curr_page ? sessionStorage.removeItem('curr_page') : '';
                    }
                }).catch(err => { that.$message.error(err); });
        },

        /**
         * 添加
         */
        add: function () {
            let that = this;
            commJS.save_page(that)
            that.$router.push({
                path: '/NursingHome/NursingHome_add'
            });
        },

        /**
         * 修改
         */
        edit: function (id) {
            let that = this;
            commJS.save_page(that)
            that.$router.push({
                path: '/NursingHome/NursingHome_add',
                query: {
                    id,
                }
            });
        },


        handleSelectionChange(val) {
            this.multipleSelection = val;
        },

        //  批量删除
        delete_all() {
            if (this.multipleSelection.length === 0) {
                return this.$message.error('请选择要删除的内容~');
            }
            var that = this;
            that.$confirm('此操作将永久删除该项, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                let ids = this.multipleSelection.map(item => item.id)
                that.axios.post("/Content/del_all", { ids }).then(res => {
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${'删除成功'}`
                    });
                    if (that.tableData.length == 1 && that.page > 1) {
                        sessionStorage.setItem('curr_page', that.page - 1)
                    }
                    that.getList();
                })
            }).catch();
        },

        /**
         * 删除
         */
        del_item: function (e) {
            var that = this;
            that.$confirm('此操作将永久删除该项, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                var formData = {}
                formData.id = e;
                that.axios.post('/bead_house/del', formData, {
                    emulateJSON: true
                }).then(
                    function (res) {
                        if (!res.status) {
                            return that.$message.error(res.msg);
                        }
                        that.$message({
                            type: 'success',
                            message: `操作提示: ${res.msg}`
                        });
                        if (that.tableData.length == 1 && that.page > 1) {
                            sessionStorage.setItem('curr_page', that.page - 1)
                        }
                        that.getList();
                    }).catch(err => { that.$message.error(err); });
            })
        },

        /**
         * 下一页
         */
        handleCurrentChange(currentPage) {
            var that = this;
            that.page = currentPage;
            that.getList();
        },
        /**
         * 搜索
         */
        search() {
            var that = this;
            that.page = 1;
            that.getList();
        }

    }
}